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
