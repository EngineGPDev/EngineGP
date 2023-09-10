<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if ($go) {
    $aGames = ['cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc'];

    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : '';
    $aData['cs'] = isset($_POST['cs']) ? trim((string) $_POST['cs']) : 0;
    $aData['cssold'] = $_POST['cssold'] ?? 0;
    $aData['css'] = $_POST['css'] ?? 0;
    $aData['csgo'] = $_POST['csgo'] ?? 0;
    $aData['samp'] = $_POST['samp'] ?? 0;
    $aData['crmp'] = $_POST['crmp'] ?? 0;
    $aData['mta'] = $_POST['mta'] ?? 0;
    $aData['mc'] = $_POST['mc'] ?? 0;
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

    foreach ($aGames as $game)
        $aData[$game] = (string)$aData[$game] == 'on' ? '1' : '0';

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    foreach ($aGames as $game) {
        if (!$aData[$game])
            continue;

        $sql->query('INSERT INTO `plugins_category` set '
            . '`game`="' . $game . '",'
            . '`name`="' . htmlspecialchars($aData['name']) . '",'
            . '`sort`="' . $aData['sort'] . '"');
    }

    sys::outjs(['s' => 'ok']);
}

$html->get('addcat', 'sections/addons');

$html->pack('main');
