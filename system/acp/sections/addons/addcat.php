<?php

/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

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
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

    foreach ($aGames as $game) {
        $aData[$game] = (string)$aData[$game] == 'on' ? '1' : '0';
    }

    if (in_array('', $aData)) {
        sys::outjs(['e' => 'Необходимо заполнить все поля']);
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

    sys::outjs(['s' => 'ok']);
}

$html->get('addcat', 'sections/addons');

$html->pack('main');
