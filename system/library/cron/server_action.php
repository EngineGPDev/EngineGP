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

class server_action extends cron
{
    public function __construct()
    {
        global $argv, $mcache;

        $nmch = 'cron_server_action_' . $argv[5];

        if ($mcache->get($nmch)) {
            return null;
        }

        $mcache->set($nmch, true, false, 10);

        if ($argv[3] == 'console') {
            global $sql;

            $sql->query('SELECT `uid`, `unit` FROM `servers` WHERE `id`="' . $argv[5] . '" LIMIT 1');
            $server = $sql->get();

            include(LIB . 'ssh.php');

            $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
            $unit = $sql->get();

            // Проверка ssh соедниения пу с локацией
            if (!$ssh->auth($unit['passwd'], $unit['address'])) {
                return null;
            }

            $sql->query('SELECT `commands` FROM `crontab` WHERE `id`="' . $argv[6] . '" LIMIT 1');
            $cron = $sql->get();

            $aCmd = explode("\n", base64_decode($cron['commands']));

            foreach ($aCmd as $cmd) {
                $ssh->set('sudo -u server' . $server['uid'] . ' tmux send-keys -t s_' . $server['uid'] . ' "' . sys::cmd($cmd) . '" C-m');
            }

            return null;
        }

        include(LIB . 'games/' . $argv[4] . '/action.php');

        if ($argv[3] == 'restart') {
            action::start($argv[5], 'restart');
        } else {
            action::start($argv[5]);
        }

        return null;
    }
}
