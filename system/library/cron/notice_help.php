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

if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

class notice_help extends cron
{
    function __construct()
    {
        global $cfg, $sql, $start_point;

        $time = $start_point - 3600;

        $helps = $sql->query('SELECT `id`, `user`, `time` FROM `help` WHERE `status`="0" AND `time`<"' . $time . '" AND `notice`="0" AND `close`="0"');
        while ($help = $sql->get($helps)) {
            $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $help['user'] . '" AND `time`<"' . $help['time'] . '" AND `notice_help`="1" LIMIT 1');

            if (!$sql->num())
                continue;

            $user = $sql->get();

            if (!sys::mail('Техническая поддержка', sys::updtext(sys::text('mail', 'notice_help'), array('site' => $cfg['name'], 'url' => $cfg['http'] . 'help/section/dialog/id/' . $help['id'])), $user['mail']))
                continue;

            $sql->query('UPDATE `help` set `notice`="1" WHERE `id`="' . $help['id'] . '" LIMIT 1');
        }

        return NULL;
    }
}
