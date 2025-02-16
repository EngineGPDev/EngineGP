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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class threads extends cron
{
    public function __construct()
    {
        global $sql, $cfg, $argv, $start_point;

        $aUnit = [];
        $sql->query('SELECT `id` FROM `units` ORDER BY `id` ASC');

        if (!$sql->num()) {
            return null;
        }

        while ($unit = $sql->get()) {
            $aUnit[$unit['id']] = [];
        }

        $sql->query('SELECT `id` FROM `servers` LIMIT 1');

        if (!$sql->num()) {
            return null;
        }

        $sql->query('SELECT `id`, `unit`, `game` FROM `servers` ORDER BY `unit` DESC');

        $all = $sql->num();

        while ($server = $sql->get()) {
            $aUnit[$server['unit']][$server['game']] ??= [];
            $aUnit[$server['unit']][$server['game']][] = $server['id'];
        }

        if ($argv[3] == 'scan_servers_route') {
            cron::$seping = 50;
        }

        foreach ($aUnit as $unit => $aGame) {
            foreach ($aGame as $game => $servers) {
                if (is_array($servers)) {
                    $servers = implode(' ', $servers);
                }
                $aData = explode(' ', $servers);

                $num = count($aData) - 1;
                $sep = $num > 0 ? ceil($num / cron::$seping) : 1;

                unset($aData[end($aData)]);

                $threads[] = cron::thread($sep, $game . ' ' . $unit, $aData);
            }
        }

        $cmd = '';

        foreach ($threads as $thread) {
            foreach ($thread as $tmux => $servers) {
                $cmd .= 'sudo -u www-data tmux new-session -ds scan_' . (System::first(explode(' ', $servers))) . '_' . $tmux . ' sh -c \"cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' ' . $argv[3] . ' ' . $servers . '\"; sleep 1;';
            }
        }

        exec('tmux new-session -ds threads_' . date('His', $start_point) . ' sh -c "' . $cmd . '"');

        return null;
    }
}
