<?php

/*
 * Copyright 2018-2025 Solovev Sergei
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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$html->nav('Списки карт');

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

if (!isset($ssh)) {
    include(LIB . 'ssh.php');
}

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go) {
        System::outjs(['e' => System::text('error', 'ssh')], $nmch);
    }

    System::back($cfg['http'] . 'servers/id/' . $id . '/section/maps');
}

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

// Директория сервера
$dir = $tarif['install'] . $server['uid'] . '/csgo/';

// Генерация списка
if ($go and isset($url['gen'])) {
    $ssh->set('cd ' . $dir . 'maps/ && du -ah | grep -e "\.bsp$" | awk \'{print $2}\'');

    $maps = $ssh->get();

    $aMaps = explode("\n", str_ireplace(['./', '.bsp'], '', $maps));

    sort($aMaps);
    reset($aMaps);

    $list = '';

    foreach ($aMaps as $index => $map) {
        $aMap = explode('/', $map);
        $name = end($aMap);
        if (strlen($name) < 4) {
            continue;
        }

        $list .= $map . "\n";
    }

    System::outjs(['s' => $list], $nmch);
}

$aFiles = [
    'mapcycle' => 'mapcycle.txt',
    'maps' => 'maplist.txt',
];

// Сохранение
if ($go and isset($url['file'])) {
    if (!array_key_exists($url['file'], $aFiles)) {
        exit;
    }

    $data = $_POST['data'] ?? '';

    $temp = System::temp($data);

    // Отправление файла на сервер
    $ssh->setfile($temp, $dir . $aFiles[$url['file']]);
    $ssh->set('chmod 0644' . ' ' . $dir . $aFiles[$url['file']]);

    // Смена владельца/группы файла
    $ssh->set('chown server' . $server['uid'] . ':servers ' . $dir . $aFiles[$url['file']]);

    unlink($temp);

    System::outjs(['s' => 'ok'], $nmch);
}

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $dir . $aFiles['mapcycle'] . '; cat ' . $dir . $aFiles['mapcycle'] . '"');
$mapcycle = $ssh->get();

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $dir . $aFiles['maps'] . '; cat ' . $dir . $aFiles['maps'] . '"');
$maps = $ssh->get();

$html->get('listing', 'sections/servers/' . $server['game'] . '/maps');

$html->set('id', $id);

$html->set('mapcycle', $mapcycle);
$html->set('maps', $maps);

$html->pack('main');
