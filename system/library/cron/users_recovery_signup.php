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

class users_recovery_signup extends cron
{
    function __construct()
    {
        global $sql, $start_point;

        $time = $start_point - 86400;

        $sql->query('DELETE FROM `signup` WHERE `date`<"' . $time . '"');
        $sql->query('DELETE FROM `recovery` WHERE `date`<"' . $time . '"');

        return NULL;
    }
}
