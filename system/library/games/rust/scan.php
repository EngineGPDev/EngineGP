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
            'type' => 'rust',
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
        $out['image'] = '<img src="' . sys::status('working', $server['game'], $info['map'], 'img') . '">';
        $out['buttons'] = sys::buttons($id, 'working', $server['game']);
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
            $aData[$i]['score'] = sys::int($player['gq_score']);
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
