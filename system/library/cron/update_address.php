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

class update_address extends cron
{
    public function __construct()
    {
        global $cfg, $sql, $start_point;

        $add_buys = $sql->query('SELECT `id`, `aid`, `server` FROM `address_buy` WHERE `time`<"' . $start_point . '"');

        while ($add_buy = $sql->get($add_buys)) {
            $sql->query('SELECT `unit`, `port`, `game`, `status` FROM `servers` WHERE `id`="' . $add_buy['server'] . '" LIMIT 1');
            if ($sql->num()) {
                $server = $sql->get();

                if (!$cfg['buy_address'][$server['game']]) {
                    continue;
                }

                $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
                $unit = $sql->get();

                include(LIB . 'games/games.php');

                // Очистка правил FireWall
                games::iptables($add_buy['server'], 'remove', null, null, null, $server['unit'], false);

                $sql->query('UPDATE `servers` set `address`="' . (sys::first(explode(':', $unit['address']))) . ':' . $server['port'] . '" WHERE `id`="' . $add_buy['server'] . '" LIMIT 1');

                if (in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
                    exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' server_action restart ' . $server['game'] . ' ' . $add_buy['server'] . '"');
                }
            }

            $sql->query('UPDATE `address` set `buy`="0" WHERE `id`="' . $add_buy['aid'] . '" LIMIT 1');
            $sql->query('DELETE FROM `address_buy` WHERE `id`="' . $add_buy['id'] . '" LIMIT 1');
        }

        return null;
    }
}
