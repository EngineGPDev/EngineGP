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

class scan_control extends cron
{
    function __construct()
    {
        global $cfg, $sql;

        include(LIB . 'ssh.php');
        include(LIB . 'control/control.php');

        $sql->query('SELECT `id` FROM `control` ORDER BY `id` ASC');
        while ($ctrl = $sql->get())
            ctrl::update_status($ctrl['id'], $ssh);

        return NULL;
    }
}
