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

class graph_servers_day extends cron
{
    public function __construct()
    {
        global $sql, $start_point;

        $servers = $sql->query('SELECT `id`, `date` FROM `servers` ORDER BY `id` ASC');

        while ($server = $sql->get($servers)) {
            if ($server['date'] + 86400 > $start_point) {
                continue;
            }

            $aGraph = ['online' => 0, 'cpu' => 0, 'ram' => 0, 'hdd' => 0, 'time' => 0];

            $sql->query('SELECT `online`, `cpu`, `ram`, `hdd` FROM `graph_hour` WHERE `server`="' . $server['id'] . '" AND `time`>"' . ($start_point - 86400) . '" ORDER BY `id` DESC LIMIT 24');

            $n = $sql->num();

            if (!$n) {
                continue;
            }

            while ($graph = $sql->get()) {
                $aGraph['online'] += $graph['online'];
                $aGraph['cpu'] += $graph['cpu'];
                $aGraph['ram'] += $graph['ram'];
                $aGraph['hdd'] += $graph['hdd'];
            }

            $aGraph['online'] = $aGraph['online'] / $n;
            $aGraph['cpu'] = $aGraph['cpu'] / $n;
            $aGraph['ram'] = $aGraph['ram'] / $n;
            $aGraph['hdd'] = $aGraph['hdd'] / $n;

            $sql->query('INSERT INTO `graph_day` set `server`="' . $server['id'] . '",'
                . '`online`="' . $aGraph['online'] . '",'
                . '`cpu`="' . $aGraph['cpu'] . '",'
                . '`ram`="' . $aGraph['ram'] . '",'
                . '`hdd`="' . $aGraph['hdd'] . '", `time`="' . $start_point . '"');
        }

        return null;
    }
}
