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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class scan_servers_down extends cron
{
    public function __construct()
    {
        global $cfg, $sql, $argv, $start_point;

        $servers = $argv;

        unset($servers[0], $servers[1], $servers[2]);

        $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $servers[4] . '" LIMIT 1');
        if (!$sql->num()) {
            return null;
        }

        $unit = $sql->get();

        $game = $servers[3];

        if (!array_key_exists($game, cron::$quakestat)) {
            return null;
        }

        unset($servers[3], $servers[4]);

        $sql->query('SELECT `unit` FROM `servers` WHERE `id`="' . $servers[5] . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd`, `ram` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        include(LIB . 'ssh.php');

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return null;
        }

        foreach ($servers as $id) {
            $sql->query('SELECT `uid`, `address`, `port`, `status`, `autorestart` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
            $server = $sql->get();

            $server_address = $server['address'] . ':' . $server['port'];

            if (!$server['autorestart']) {
                continue;
            }

            if ($server['status'] != 'working') {
                continue;
            }

            if (!in_array(trim($ssh->get('quakestat -' . (cron::$quakestat[$game]) . ' ' . $server_address . ' -retry 5 -interval 2 | grep -v frags | tail -1 | awk \'{print $2}\'')), ['DOWN', 'no'])) {
                continue;
            }

            exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' server_action restart ' . $game . ' ' . $id . '"');

            $sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="' . $id . '", `text`="Перезагрука сервера: сервер завис", `time`="' . $start_point . '"');
        }

        return null;
    }
}
