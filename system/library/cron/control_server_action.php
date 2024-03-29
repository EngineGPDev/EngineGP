<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

class control_server_action extends cron
{
    function __construct()
    {
        global $argv, $mcache;

        $nmch = 'cron_server_action_' . $argv[5];

        if ($mcache->get($nmch))
            return NULL;

        $mcache->set($nmch, true, false, 10);

        if ($argv[3] == 'console') {
            global $sql;

            $sql->query('SELECT `uid`, `unit` FROM `control_servers` WHERE `id`="' . $argv[5] . '" LIMIT 1');
            $server = $sql->get();

            include(LIB . 'ssh.php');

            $sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
            $unit = $sql->get();

            // Проверка ssh соедниения пу с локацией
            if (!$ssh->auth($unit['passwd'], $unit['address']))
                return NULL;

            $sql->query('SELECT `commands` FROM `control_crontab` WHERE `id`="' . $argv[6] . '" LIMIT 1');
            $cron = $sql->get();

            $aCmd = explode("\n", base64_decode($cron['commands']));

            foreach ($aCmd as $cmd)
                $ssh->set('sudo -u server' . $server['uid'] . ' screen -p 0 -S s_' . $server['uid'] . ' -X eval \'stuff "' . sys::cmd($cmd) . '"\015\'; sudo -u server' . $server['uid'] . ' screen -p 0 -S s_' . $server['uid'] . ' -X eval \'stuff \015\'');

            return NULL;
        }

        include(LIB . 'control/' . $argv[4] . '/action.php');

        if ($argv[3] == 'restart')
            action::start($argv[5], 'restart');
        else
            action::$argv[3]($argv[5]);

        return NULL;
    }
}

?>