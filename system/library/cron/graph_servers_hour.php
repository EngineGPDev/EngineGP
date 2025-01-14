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

class graph_servers_hour extends cron
{
    public function __construct()
    {
        global $sql, $start_point;

        $servers = $sql->query('SELECT `id`, `online`, `ram_use`, `cpu_use`, `hdd_use`, `date` FROM `servers` ORDER BY `id` ASC');

        while ($server = $sql->get($servers)) {
            if ($server['date'] + 3600 > $start_point) {
                continue;
            }

            $sql->query('INSERT INTO `graph_hour` set `server`="' . $server['id'] . '",'
                . '`online`="' . $server['online'] . '",'
                . '`cpu`="' . $server['cpu_use'] . '",'
                . '`ram`="' . $server['ram_use'] . '",'
                . '`hdd`="' . $server['hdd_use'] . '", `time`="' . $start_point . '"');
        }

        return null;
    }
}
