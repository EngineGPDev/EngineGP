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

class notice_server_overdue extends cron
{
    public function __construct()
    {
        global $cfg, $sql, $start_point;

        $servers = $sql->query('SELECT `id`, `user`, `address`, `port` FROM `servers` WHERE `time`<"' . $start_point . '" AND `mail`="0"');
        while ($server = $sql->get($servers)) {
            $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $server['user'] . '" LIMIT 1');
            $user = $sql->get();

            $server_address = $server['address'] . ':' . $server['port'];

            if (!sys::mail('Аренда сервера', sys::updtext(sys::text('mail', 'notice_server_overdue'), ['site' => $cfg['name'], 'id' => $server['id'], 'address' => $server_address]), $user['mail'])) {
                continue;
            }

            $sql->query('UPDATE `servers` set `mail`="1" WHERE `id`="' . $server['id'] . '" LIMIT 1');
        }

        $servers = $sql->query('SELECT `id` FROM `servers` WHERE `time`>"' . $start_point . '" AND `mail`="1"');
        while ($server = $sql->get($servers)) {
            $sql->query('UPDATE `servers` set `mail`="0" WHERE `id`="' . $server['id'] . '" LIMIT 1');
        }

        return null;
    }
}
