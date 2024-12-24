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

$sql->query('SELECT * FROM `units` WHERE `id`="' . $id . '" LIMIT 1');
$unit = $sql->get();

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $unit['name'];
    $aData['address'] = isset($_POST['address']) ? trim($_POST['address']) : $unit['address'];
    $aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : $unit['passwd'];
    $aData['sql_login'] = isset($_POST['sql_login']) ? trim($_POST['sql_login']) : $unit['sql_login'];
    $aData['sql_passwd'] = isset($_POST['sql_passwd']) ? trim($_POST['sql_passwd']) : $unit['sql_passwd'];
    $aData['sql_port'] = isset($_POST['sql_port']) ? sys::int($_POST['sql_port']) : $unit['sql_port'];
    $aData['sql_ftp'] = isset($_POST['sql_ftp']) ? trim($_POST['sql_ftp']) : $unit['sql_ftp'];
    $aData['cs'] = $_POST['cs'] ?? $unit['cs'];
    $aData['cssold'] = $_POST['cssold'] ?? $unit['cssold'];
    $aData['css'] = $_POST['css'] ?? $unit['css'];
    $aData['csgo'] = $_POST['csgo'] ?? $unit['csgo'];
    $aData['cs2'] = $_POST['cs2'] ?? $unit['cs2'];
    $aData['rust'] = $_POST['rust'] ?? $unit['rust'];
    $aData['samp'] = $_POST['samp'] ?? $unit['samp'];
    $aData['crmp'] = $_POST['crmp'] ?? $unit['crmp'];
    $aData['mta'] = $_POST['mta'] ?? $unit['mta'];
    $aData['mc'] = $_POST['mc'] ?? $unit['mc'];
    $aData['ram'] = isset($_POST['ram']) ? sys::int($_POST['ram']) : $unit['ram'];
    $aData['test'] = isset($_POST['test']) ? sys::int($_POST['test']) : $unit['test'];
    $aData['show'] = $_POST['show'] ?? $unit['show'];
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : $unit['sort'];
    $aData['domain'] = isset($_POST['domain']) ? trim($_POST['domain']) : $unit['domain'];

    foreach (['cs', 'cssold', 'css', 'csgo', 'cs2', 'rust', 'samp', 'crmp', 'mta', 'mc'] as $game) {
        $aData[$game] = (string)$aData[$game] == 'on' ? '1' : '0';
    }

    if (in_array('', $aData)) {
        sys::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    include(LIB . 'ssh.php');

    if (!$ssh->auth($aData['passwd'], $aData['address'])) {
        sys::outjs(['e' => 'Не удалось создать связь с локацией']);
    }

    $sql->query('UPDATE `units` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`address`="' . $aData['address'] . '",'
        . '`passwd`="' . $aData['passwd'] . '",'
        . '`sql_login`="' . $aData['sql_login'] . '",'
        . '`sql_passwd`="' . $aData['sql_passwd'] . '",'
        . '`sql_port`="' . $aData['sql_port'] . '",'
        . '`sql_ftp`="' . $aData['sql_ftp'] . '",'
        . '`cs`="' . $aData['cs'] . '",'
        . '`cssold`="' . $aData['cssold'] . '",'
        . '`css`="' . $aData['css'] . '",'
        . '`csgo`="' . $aData['csgo'] . '",'
        . '`cs2`="' . $aData['cs2'] . '",'
        . '`rust`="' . $aData['rust'] . '",'
        . '`samp`="' . $aData['samp'] . '",'
        . '`crmp`="' . $aData['crmp'] . '",'
        . '`mta`="' . $aData['mta'] . '",'
        . '`mc`="' . $aData['mc'] . '",'
        . '`ram`="' . $aData['ram'] . '",'
        . '`test`="' . $aData['test'] . '",'
        . '`show`="' . $aData['show'] . '",'
        . '`sort`="' . $aData['sort'] . '",'
        . '`domain`="' . $aData['domain'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    sys::outjs(['s' => $id]);
}

$html->get('unit', 'sections/units');

foreach ($unit as $i => $val) {
    $html->set($i, $val);
}

foreach (['cs', 'cssold', 'css', 'csgo', 'cs2', 'rust', 'samp', 'crmp', 'mta', 'mc'] as $game) {
    if ($unit[$game]) {
        $html->unit('game_' . $game, 1);
    } else {
        $html->unit('game_' . $game);
    }
}

$html->set('show', $unit['show'] == 1 ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>');

$html->pack('main');
