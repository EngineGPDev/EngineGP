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

class scan_servers_admins extends cron
{
    public function __construct()
    {
        global $sql, $argv, $start_point;

        $servers = $argv;

        unset($servers[0], $servers[1], $servers[2]);

        $game = $servers[3];

        if (!array_key_exists($game, cron::$admins_file)) {
            return null;
        }

        $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $servers[4] . '" LIMIT 1');
        if (!$sql->num()) {
            return null;
        }

        $unit = $sql->get();

        unset($servers[3], $servers[4]);

        $sql->query('SELECT `unit` FROM `servers` WHERE `id`="' . $servers[5] . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        include(LIB . 'ssh.php');

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return null;
        }

        foreach ($servers as $id) {
            $sql->query('SELECT `uid`, `tarif` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
            $server = $sql->get();

            $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
            $tarif = $sql->get();

            $admins = $sql->query('SELECT `id`, `text` FROM `admins_' . $game . '` WHERE `server`="' . $id . '" AND `active`="1" AND `time`<"' . $start_point . '"');

            if (!$sql->num($admins)) {
                continue;
            }

            $cmd = 'cd ' . $tarif['install'] . $server['uid'] . ';';

            while ($admin = $sql->get($admins)) {
                $cmd .= 'sed -i -e \'s/' . escapeshellcmd(htmlspecialchars_decode($admin['text'])) . '//g\' ' . cron::$admins_file[$game] . ';';

                $sql->query('UPDATE `admins_' . $game . '` set `active`="0" WHERE `id`="' . $admin['id'] . '" LIMIT 1');
            }

            $cmd .= 'sed -i ' . "'/./!d'" . ' ' . cron::$admins_file[$game] . '; echo -e "\n" >> ' . cron::$admins_file[$game] . ';';
            $cmd .= 'chown server' . $server['uid'] . ':servers ' . cron::$admins_file[$game];

            $ssh->set($cmd);
        }

        return null;
    }
}
