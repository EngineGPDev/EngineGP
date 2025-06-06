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
use GameQ\GameQ;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

include(LIB . 'games/scans.php');

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
            'type' => 'css',
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
            $out['time_end'] = 'Удаление через: ' . System::date('min', $server['overdue'] + $cfg['server_delete'] * 86400);
        } else {
            $out['time_end'] = 'Осталось: ' . System::date('min', $server['time']);
        }

        if (!$info || $info['gq_online'] === false) {
            $out['name'] = $server['name'];
            $out['status'] = System::status($server['status'], $server['game'], $server['map']);
            $out['online'] = $server['online'];
            $out['image'] = '<img src="' . System::status($server['status'], $server['game'], $server['map'], 'img') . '">';
            $out['buttons'] = System::buttons($id, $server['status']);

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
        $out['status'] = System::status('working', $server['game'], $info['map']);
        $out['online'] = System::int($info['gq_numplayers']);
        $out['image'] = '<img src="' . System::status('working', $server['game'], $info['map'], 'img') . '">';
        $out['buttons'] = System::buttons($id, 'working');
        $out['players'] = '';

        if ($players_get) {
            foreach ($players as $index => $player) {
                $html->get($server['game'], 'sections/servers/players');

                $html->set('i', $player['i']);
                $html->set('name', $player['name']);
                $html->set('score', $player['score']);
                $html->set('time', $player['time']);

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
            $aData[$i]['score'] = System::int($player['gq_score']);
            $aData[$i]['time'] = scan::formatTime($player['gq_time']);

            $i += 1;
        }

        return $aData;
    }

    public static function formatTime($seconds)
    {
        $seconds = intval($seconds);

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
