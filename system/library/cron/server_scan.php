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

class server_scan extends cron
{
    function __construct()
    {
        global $argv;

        include(LIB . 'games/' . $argv[3] . '/scan.php');

        scan::mon($argv[4], true);
        scan::resources($argv[4]);

        return NULL;
    }
}
