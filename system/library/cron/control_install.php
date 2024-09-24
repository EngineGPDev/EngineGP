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

class control_install extends cron
{
    public function __construct()
    {
        global $cfg, $sql, $argv;

        $sql->query('SELECT `id`, `address`, `passwd` FROM `control` WHERE `status`="install" AND `install`="0" LIMIT 1');

        if (!$sql->num()) {
            exit('not found');
        }

        $unit = $sql->get();

        include(LIB . 'ssh.php');

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            exit('error connect');
        }

        $ssh->set('apt-get update; apt-get install -y wget screen');

        sleep(20);

        $ssh->set('screen -dmS install bash -c "cd /tmp; rm script.sh; wget -O script.sh [home]autocontrol/action/script --no-check-certificate; chmod 500 script.sh;./script.sh"');

        $sql->query('UPDATE `control` set install="1" WHERE `id`="' . $unit['id'] . '" LIMIT 1');

        exit('install');
    }
}
