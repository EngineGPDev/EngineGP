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

// Редактируемый файл
$file = $url['file'] ?? sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');

include(DATA . 'filedits.php');

// Проверка наличия в конфиге
if (!in_array($file, $aEdits[$server['game']]['all']['files']) && !in_array($file, $aEdits[$server['game']][$tarif['name']]['files'])) {
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

$html->nav('Редактирование файла: ' . $file);

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go) {
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
    }

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

// Полный путь файла
$path = $tarif['install'] . $server['uid'] . '/' . $aEdits[$server['game']]['all']['path'][$file] . $file;
if ($go) {
    $data = $_POST['data'] ?? '';

    $temp = sys::temp($data);

    // Отправление файла на сервер
    $ssh->setfile($temp, $path);
    $ssh->set('chmod 0644' . ' ' . $path);

    // Смена владельца/группы файла
    $ssh->set('chown server' . $server['uid'] . ':servers ' . $path);

    unlink($temp);

    sys::outjs(['s' => 'ok'], $nmch);
}

$ssh->set('sudo -u server' . $server['uid'] . ' sh -c "touch ' . $path . '; cat ' . $path . '"');

$html->get('file', 'sections/servers/games/settings');

$html->set('id', $id);
$html->set('file', $file);
$html->set('data', htmlspecialchars($ssh->get()));

$html->pack('main');
