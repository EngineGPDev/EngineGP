<?php

/*
 * Copyright 2018-2024 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}
if (!$go) {
    exit;
}

$sql->query('SELECT `unit`, `tarif`, `map_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

if (!isset($ssh)) {
    include(LIB . 'ssh.php');
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
}

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$dir = $tarif['install'] . $server['uid'] . '/game/csgo/';

// Генерация списка карт
$ssh->set('cd ' . $dir . 'maps/ && ls | grep -iE "\.vpk$"');

$maps = $ssh->get();

$aMaps = explode("\n", str_ireplace('.vpk', '', $maps));

// Массив переданных карт
$in_aMaps = $_POST['maps'] ?? [];

// Обработка выборки
foreach ($in_aMaps as $name => $sel) {
    if ($sel) {
        $map = str_replace(["\\", "'", "'", '-_-'], ['', '', '', '$'], $name);

        // Проверка наличия карты
        if (!in_array($map, $aMaps)) {
            continue;
        }

        // Проверка: является ли карта стартовой
        if ($server['map_start'] == $map) {
            continue;
        }

        $ssh->set('cd /path/maps/' . $server['game'] . '/' . sys::map($map) . ' && du -a | grep -iE "\.[a-z]{1,3}$" | awk \'{print $2}\'');

        $aFiles = explode("\n", str_replace('./', '', $ssh->get()));

        if (isset($aFiles[count($aFiles) - 1]) and $aFiles[count($aFiles) - 1] == '') {
            unset($aFiles[count($aFiles) - 1]);
        }

        $files = '';

        foreach ($aFiles as $file) {
            $files .= $dir . $file . ' ';
        }

        $rm = '';
        $aFlrm = explode(' ', $dir . 'maps/' . $map . '.* ' . trim($files));

        foreach ($aFlrm as $flrm) {
            $rm .= sys::map($flrm) . ' ';
        }

        $ssh->set('sudo -u server' . $server['uid'] . ' tmux new-session -ds md' . $start_point . $id . ' sh -c \'rm ' . trim($rm) . '\'');
    }
}

sys::outjs(['s' => 'ok'], $nmch);
