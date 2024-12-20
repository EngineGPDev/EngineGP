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

class scan_servers_copy extends cron
{
    public function __construct()
    {
        global $sql, $argv, $start_point;

        $servers = $argv;

        unset($servers[0], $servers[1], $servers[2]);

        $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $servers[4] . '" LIMIT 1');
        if (!$sql->num()) {
            return null;
        }

        $unit = $sql->get();

        $game = $servers[3];

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
            $copys = $sql->query('SELECT `id` FROM `copy` WHERE `status`="0"');
            while ($copy = $sql->get($copys)) {
                $sql->query('SELECT `uid` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
                $server = $sql->get();

                if (!sys::int($ssh->get('ps aux | grep copy_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\''))) {
                    $sql->query('UPDATE `copy` set `status`="1" WHERE `id`="' . $copy['id'] . '" LIMIT 1');
                }
            }
        }

        return null;
    }
}
