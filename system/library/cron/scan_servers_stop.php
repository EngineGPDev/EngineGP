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

class scan_servers_stop extends cron
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

        $autostop = $start_point - $cfg['autostop'] * 60;
        $teststop = $start_point - $cfg['teststop'] * 60;

        $sqlq = '(`test`="1" AND `time_start`<"' . $teststop . '" OR `autostop`="1" AND `time_start`<"' . $autostop . '")';

        foreach ($servers as $id) {
            $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" AND `status`="working" AND `online`="0" AND ' . $sqlq . ' LIMIT 1');

            if (!$sql->num()) {
                continue;
            }

            exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' server_action stop ' . $game . ' ' . $id . '"');

            $sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="' . $id . '", `text`="Выключение сервера: на сервере нет игроков", `time`="' . $start_point . '"');
        }

        return null;
    }
}
