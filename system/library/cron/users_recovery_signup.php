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

class users_recovery_signup extends cron
{
    public function __construct()
    {
        global $sql, $start_point;

        $time = $start_point - 86400;

        $sql->query('DELETE FROM `signup` WHERE `date`<"' . $time . '"');
        $sql->query('DELETE FROM `recovery` WHERE `date`<"' . $time . '"');

        return null;
    }
}
