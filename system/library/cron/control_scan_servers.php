<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

class control_scan_servers extends cron
{
    function __construct()
    {
        global $cfg, $sql, $argv, $start_point, $mcache;

        $servers = $argv;

        unset($servers[0], $servers[1], $servers[2]);

        $sql->query('SELECT `address` FROM `control` WHERE `id`="' . $servers[4] . '" LIMIT 1');
        if (!$sql->num())
            return NULL;

        $unit = $sql->get();

        $game = $servers[3];

        unset($servers[3], $servers[4]);

        $sql->query('SELECT `unit` FROM `control_servers` WHERE `id`="' . $servers[5] . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        include(LIB . 'ssh.php');

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address']))
            return NULL;

        foreach ($servers as $id) {
            $sql->query('SELECT `uid`, `address`, `status`, `stop` FROM `control_servers` WHERE `id`="' . $id . '" LIMIT 1');
            $server = $sql->get();

            switch ($server['status']) {
                case 'working':
                case 'change':
                case 'start':
                case 'restart':
                    if (!sys::int($ssh->get('ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\''))) {
                        $sql->query('UPDATE `control_servers` set `status`="off", `online`="0", `players`="0" WHERE `id`="' . $id . '" LIMIT 1');

                        // Запуск сервера (если он был выключен не через панель)
                        if ($server['stop']) {
                            exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' control_server_action start ' . $game . ' ' . $id . '"');

                            $sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="' . $id . '", `text`="[Контроль] Включение сервера: сервер выключен не через панель", `time`="' . $start_point . '"');
                        }
                    } else
                        exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' control_server_scan ' . $game . ' ' . $id . '"');

                    break;

                case 'off':
                    if (sys::int($ssh->get('ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\'')))
                        $sql->query('UPDATE `control_servers` set `status`="working" WHERE `id`="' . $id . '" LIMIT 1');
                    else {
                        // Запуск сервера (если он был выключен не через панель)
                        if ($server['stop']) {
                            exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' control_server_action start ' . $game . ' ' . $id . '"');

                            $sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="' . $id . '", `text`="[Контроль] Включение сервера: сервер выключен не через панель", `time`="' . $start_point . '"');

                            continue;
                        }
                    }

                    break;

                case 'reinstall':
                    if (!sys::int($ssh->get('ps aux | grep r_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\'')))
                        $sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="' . $id . '" LIMIT 1');

                    break;

                case 'update':
                    if (!sys::int($ssh->get('ps aux | grep u_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\'')))
                        $sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="' . $id . '" LIMIT 1');

                    break;

                case 'install':
                    if (!sys::int($ssh->get('ps aux | grep i_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\'')))
                        $sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="' . $id . '" LIMIT 1');

                    break;

                case 'recovery':
                    if (!sys::int($ssh->get('ps aux | grep rec_' . $server['uid'] . ' | grep -v grep | awk \'{print $2}\'')))
                        $sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="' . $id . '" LIMIT 1');
            }
        }

        return NULL;
    }
}
