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
