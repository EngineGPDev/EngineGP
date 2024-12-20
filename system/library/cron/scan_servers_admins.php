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
