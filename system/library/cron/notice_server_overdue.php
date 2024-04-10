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

class notice_server_overdue extends cron
{
    function __construct()
    {
        global $cfg, $sql, $start_point;

        $servers = $sql->query('SELECT `id`, `user`, `address` FROM `servers` WHERE `time`<"' . $start_point . '" AND `mail`="0"');
        while ($server = $sql->get($servers)) {
            $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $server['user'] . '" LIMIT 1');
            $user = $sql->get();

            if (!sys::mail('Аренда сервера', sys::updtext(sys::text('mail', 'notice_server_overdue'), array('site' => $cfg['name'], 'id' => $server['id'], 'address' => $server['address'])), $user['mail']))
                continue;

            $sql->query('UPDATE `servers` set `mail`="1" WHERE `id`="' . $server['id'] . '" LIMIT 1');
        }

        $servers = $sql->query('SELECT `id` FROM `servers` WHERE `time`>"' . $start_point . '" AND `mail`="1"');
        while ($server = $sql->get($servers))
            $sql->query('UPDATE `servers` set `mail`="0" WHERE `id`="' . $server['id'] . '" LIMIT 1');

        return NULL;
    }
}
