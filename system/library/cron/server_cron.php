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

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

class server_cron extends cron
{
    function __construct()
    {
        global $argv, $sql, $cfg;

        $sql->query('SELECT `game` FROM `servers` WHERE `id`="' . $argv[3] . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `task` FROM `crontab` WHERE `id`="' . $argv[4] . '" LIMIT 1');
        $cron = $sql->get();

        $cmd = $cron['task'] == 'console' ? ' ' . $argv[4] : '';

        exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' server_action ' . $cron['task'] . ' ' . $server['game'] . ' ' . $argv[3] . $cmd . '"');

        return NULL;
    }
}
