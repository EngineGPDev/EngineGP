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
use EngineGP\AdminSystem;
use EngineGP\Infrastructure\RemoteAccess\SshClient;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['address'] = isset($_POST['address']) ? trim($_POST['address']) : '';
    $aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : '';
    $aData['sql_login'] = isset($_POST['sql_login']) ? trim($_POST['sql_login']) : '';
    $aData['sql_passwd'] = isset($_POST['sql_passwd']) ? trim($_POST['sql_passwd']) : '';
    $aData['sql_port'] = isset($_POST['sql_port']) ? AdminSystem::int($_POST['sql_port']) : 3306;
    $aData['sql_ftp'] = isset($_POST['sql_ftp']) ? trim($_POST['sql_ftp']) : '';
    $aData['cs'] = isset($_POST['cs']) ? trim($_POST['cs']) : 0;
    $aData['cssold'] = $_POST['cssold'] ?? 0;
    $aData['css'] = $_POST['css'] ?? 0;
    $aData['csgo'] = $_POST['csgo'] ?? 0;
    $aData['cs2'] = $_POST['cs2'] ?? 0;
    $aData['rust'] = $_POST['rust'] ?? 0;
    $aData['samp'] = $_POST['samp'] ?? 0;
    $aData['crmp'] = $_POST['crmp'] ?? 0;
    $aData['mta'] = $_POST['mta'] ?? 0;
    $aData['mc'] = $_POST['mc'] ?? 0;
    $aData['ram'] = isset($_POST['ram']) ? AdminSystem::int($_POST['ram']) : 0;
    $aData['test'] = isset($_POST['test']) ? AdminSystem::int($_POST['test']) : 0;
    $aData['show'] = $_POST['show'] ?? 0;
    $aData['sort'] = isset($_POST['sort']) ? AdminSystem::int($_POST['sort']) : 0;
    $aData['domain'] = isset($_POST['domain']) ? trim($_POST['domain']) : '';

    foreach (['cs', 'cssold', 'css', 'csgo', 'cs2', 'rust', 'samp', 'crmp', 'mta', 'mc'] as $game) {
        $aData[$game] = (string)$aData[$game] == 'on' ? '1' : '0';
    }

    if (in_array('', $aData)) {
        AdminSystem::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    $System = new System();
    $sshClient = new SshClient($aData['address'], 'root', $aData['passwd']);

    try {
        $sshClient->connect();
    } catch (\Exception $e) {
        System::outjs(['e' => System::text('error', 'ssh')], false);
    }

    $sql->query('INSERT INTO `units` set '
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
        . '`domain`="' . $aData['domain'] . '"');

    AdminSystem::outjs(['s' => $sql->id()]);
}

$html->get('add', 'sections/units');

$html->pack('main');
