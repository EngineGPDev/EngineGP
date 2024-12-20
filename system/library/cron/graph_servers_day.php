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
