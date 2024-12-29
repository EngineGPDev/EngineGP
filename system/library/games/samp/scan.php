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

include(LIB . 'games/scans.php');

use GameQ\GameQ;

class scan extends scans
{
    public static function mon($id, $players_get = false)
    {
        global $cfg, $sql, $html, $mcache;

        $sql->query('SELECT `address`, `port_query`, `game`, `name`, `map`, `online`, `players`, `status`, `time`, `overdue` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $ip = $server['address'];
        $port = $server['port_query'];

        // Инициализация GameQ
        $gameQ = new GameQ();
        $gameQ->addServer([
            'type' => 'samp',
            'host' => $ip . ':' . $port,
        ]);

        $results = $gameQ->process();
        $info = $results[$ip . ':' . $port] ?? null;

        if ($players_get) {
            $nmch = 'server_scan_mon_pl_' . $id;
        } else {
            $nmch = 'server_scan_mon_' . $id;
        }

        if (is_array($mcache->get($nmch))) {
            return $mcache->get($nmch);
        }

        $out = [];

        $out['time'] = 'Арендован до: ' . date('d.m.Y - H:i', $server['time']);

        if ($server['status'] == 'overdue') {
            $out['time_end'] = 'Удаление через: ' . sys::date('min', $server['overdue'] + $cfg['server_delete'] * 86400);
        } else {
            $out['time_end'] = 'Осталось: ' . sys::date('min', $server['time']);
        }

        if (!$info || $info['gq_online'] === false) {
            $out['name'] = $server['name'];
            $out['status'] = sys::status($server['status'], $server['game'], $server['map']);
            $out['online'] = $server['online'];
            $out['image'] = '<img src="' . sys::status($server['status'], $server['game'], $server['map'], 'img') . '">';
            $out['buttons'] = sys::buttons($id, $server['status'], $server['game']);

            if ($players_get) {
                $out['players'] = base64_decode($server['players'] ?? '');
            }

            $mcache->set($nmch, $out, false, $cfg['mcache_server_mon']);

            return $out;
        }

        if ($players_get) {
            $players = scan::players($info['players'] ?? []);
        }

        $info['map'] = htmlspecialchars($info['gq_mapname']);
        $out['name'] = htmlspecialchars($info['gq_hostname']);
        $out['status'] = sys::status('working', $server['game'], $info['map']);
        $out['online'] = sys::int($info['gq_numplayers']);
        $out['image'] = '<img src="' . sys::status('working', $server['game'], 'samp', 'img') . '">';
        $out['buttons'] = sys::buttons($id, 'working', $server['game']);
        $out['players'] = '';

        if ($players_get) {
            foreach ($players as $index => $player) {
                $html->get($server['game'], 'sections/servers/players');

                $html->set('i', $player['i']);
                $html->set('name', htmlspecialchars($player['name']));
                $html->set('ping', $player['ping']);
                $html->set('score', $player['score']);

                $html->pack('list');
            }

            $out['players'] = $html->arr['list'] ?? '';
        }

        $sql->query('UPDATE `servers` set '
            . '`name`="' . $out['name'] . '", '
            . '`online`="' . $out['online'] . '", '
            . '`map`="' . $info['map'] . '", '
            . '`status`="working" WHERE `id`="' . $id . '" LIMIT 1');

        if ($players_get) {
            $sql->query('UPDATE `servers` set `players`="' . base64_encode($out['players']) . '" WHERE `id`="' . $id . '" LIMIT 1');
        }

        $mcache->set($nmch, $out, false, $cfg['mcache_server_mon']);

        return $out;
    }

    public static function players($aPlayrs)
    {
        $i = 1;
        $aData = [];

        foreach ($aPlayrs as $n => $player) {
            $aData[$i]['i'] = $i;
            $aData[$i]['name'] = $player['gq_name'] == '' ? 'Подключается' : htmlspecialchars($player['gq_name']);
            $aData[$i]['ping'] = sys::int($player['gq_ping'] ?? 0);
            $aData[$i]['score'] = sys::int($player['gq_score']);

            $i += 1;
        }

        return $aData;
    }
}
