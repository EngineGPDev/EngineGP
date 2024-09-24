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
    include(LIB . 'games/' . $server['game'] . '/rcon.php');

    if (isset($url['action']) and in_array($url['action'], ['kick', 'kill'])) {
        $player = $_POST['player'] ?? sys::outjs(['e' => 'Необходимо выбрать игрока.']);

        if ($url['action'] == 'kick') {
            rcon::cmd(array_merge($server, ['id' => $id]), 'amx_kick "' . $player . '" "EGP Panel"');
        } else {
            rcon::cmd(array_merge($server, ['id' => $id]), 'amx_slay "' . $player . '"');
        }

        sys::outjs(['s' => 'ok']);
    }

    include(LIB . 'geo.php');
    $SxGeo = new SxGeo(DATA . 'SxGeoCity.dat');

    $aPlayers = rcon::players(rcon::cmd(array_merge($server, ['id' => $id])));

    foreach ($aPlayers as $i => $aPlayer) {
        $html->get('player', 'sections/servers/' . $server['game'] . '/rcon');

        $html->set('i', $i);
        $html->set('name', $aPlayer['name']);
        $html->set('steamid', $aPlayer['steamid']);
        $html->set('time', $aPlayer['time']);
        $html->set('ping', $aPlayer['ping']);
        $html->set('ip', $aPlayer['ip']);
        $html->set('ico', $aPlayer['ico']);
        $html->set('country', $aPlayer['country']);

        $html->pack('players');
    }

    sys::outjs(['s' => $html->arr['players'] ?? '']);
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Rcon управление игроками');

$html->get('rcon', 'sections/servers/' . $server['game']);

$html->set('id', $id);

$html->pack('main');
