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

class control_install extends cron
{
    function __construct()
    {
        global $cfg, $sql, $argv;

        $sql->query('SELECT `id`, `address`, `passwd` FROM `control` WHERE `status`="install" AND `install`="0" LIMIT 1');

        if (!$sql->num())
            exit('not found');

        $unit = $sql->get();

        include(LIB . 'ssh.php');

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address']))
            exit('error connect');

        $ssh->set('apt-get update; apt-get install -y wget screen');

        sleep(20);

        $ssh->set('screen -dmS install bash -c "cd /tmp; rm script.sh; wget -O script.sh [home]autocontrol/action/script --no-check-certificate; chmod 500 script.sh;./script.sh"');

        $sql->query('UPDATE `control` set install="1" WHERE `id`="' . $unit['id'] . '" LIMIT 1');

        exit('install');
    }
}
