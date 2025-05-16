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

use EngineGP\AdminSystem;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if ($go) {
    $aGames = ['cs', 'cssold', 'css', 'csgo', 'cs2', 'samp', 'crmp', 'mta', 'mc'];

    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['cs'] = isset($_POST['cs']) ? trim($_POST['cs']) : 0;
    $aData['cssold'] = $_POST['cssold'] ?? 0;
    $aData['css'] = $_POST['css'] ?? 0;
    $aData['csgo'] = $_POST['csgo'] ?? 0;
    $aData['cs2'] = $_POST['cs2'] ?? 0;
    $aData['samp'] = $_POST['samp'] ?? 0;
    $aData['crmp'] = $_POST['crmp'] ?? 0;
    $aData['mta'] = $_POST['mta'] ?? 0;
    $aData['mc'] = $_POST['mc'] ?? 0;
    $aData['sort'] = isset($_POST['sort']) ? AdminSystem::int($_POST['sort']) : 0;

    foreach ($aGames as $game) {
        $aData[$game] = (string)$aData[$game] == 'on' ? '1' : '0';
    }

    if (in_array('', $aData)) {
        AdminSystem::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    foreach ($aGames as $game) {
        if (!$aData[$game]) {
            continue;
        }

        $sql->query('INSERT INTO `plugins_category` set '
            . '`game`="' . $game . '",'
            . '`name`="' . htmlspecialchars($aData['name']) . '",'
            . '`sort`="' . $aData['sort'] . '"');
    }

    AdminSystem::outjs(['s' => 'ok']);
}

$html->get('addcat', 'sections/addons');

$html->pack('main');
