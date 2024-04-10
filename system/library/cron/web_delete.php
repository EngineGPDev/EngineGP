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

class web_delete extends cron
{
    function __construct()
    {
        global $argv, $sql;

        $sql->query('SELECT `id`, `login`, `type`, `server`, `unit` FROM `web` WHERE `id`="' . $argv[3] . '" LIMIT 1');
        $web = $sql->get();

        if ($web['type'] == 'hosting') {
            include(DATA . 'web.php');

            $result = json_decode(file_get_contents(sys::updtext($aWebUnit['isp']['account']['delete'], array('login' => $web['login']))), true);

            if (!isset($result['result']) || strtolower($result['result']) != 'ok')
                continue;

            $sql->query('DELETE FROM `web` WHERE `id`="' . $web['id'] . '" LIMIT 1');
        }

        include(LIB . 'web/free.php');

        $aData = array(
            'type' => $web['type'],
            'server' => array('id' => $web['server'], 'unit' => $web['unit'], 'user' => 0, 'game' => 'system')
        );

        web::delete($aData, false);
    }
}
