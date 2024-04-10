<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

class control_server_cron extends cron
{
    function __construct()
    {
        global $argv, $sql, $cfg;

        $sql->query('SELECT `game` FROM `control_servers` WHERE `id`="' . $argv[3] . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `task` FROM `control_crontab` WHERE `id`="' . $argv[4] . '" LIMIT 1');
        $cron = $sql->get();

        $cmd = $cron['task'] == 'console' ? ' ' . $argv[4] : '';

        exec('sh -c "cd /var/enginegp; php cron.php ' . $cfg['cron_key'] . ' control_server_action ' . $cron['task'] . ' ' . $server['game'] . ' ' . $argv[3] . $cmd . '"');

        return NULL;
    }
}
