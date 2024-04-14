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

class preparing_web_delete extends cron
{
    function __construct()
    {
        global $argv, $cfg, $sql;

        $sql->query('SELECT `id` FROM `web` WHERE `user`="0"');
        while ($web = $sql->get())
            exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' web_delete ' . $web['id'] . '"');

        return NULL;
    }
}
