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

class preparing_web_delete extends cron
{
    function __construct()
    {
        global $argv, $cfg, $sql;

        $sql->query('SELECT `id` FROM `web` WHERE `user`="0"');
        while ($web = $sql->get())
            exec('sh -c "cd /var/enginegp; php cron.php ' . $cfg['cron_key'] . ' web_delete ' . $web['id'] . '"');

        return NULL;
    }
}
