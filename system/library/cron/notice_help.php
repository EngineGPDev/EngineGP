<?php

/*
 * Copyright 2018-2025 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class notice_help extends cron
{
    public function __construct()
    {
        global $cfg, $sql, $start_point;

        $time = $start_point - 3600;

        $helps = $sql->query('SELECT `id`, `user`, `time` FROM `help` WHERE `status`="0" AND `time`<"' . $time . '" AND `notice`="0" AND `close`="0"');
        while ($help = $sql->get($helps)) {
            $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $help['user'] . '" AND `time`<"' . $help['time'] . '" AND `notice_help`="1" LIMIT 1');

            if (!$sql->num()) {
                continue;
            }

            $user = $sql->get();

            if (!System::mail('Техническая поддержка', System::updtext(System::text('mail', 'notice_help'), ['site' => $cfg['name'], 'url' => $cfg['http'] . 'help/section/dialog/id/' . $help['id']]), $user['mail'])) {
                continue;
            }

            $sql->query('UPDATE `help` set `notice`="1" WHERE `id`="' . $help['id'] . '" LIMIT 1');
        }

        return null;
    }
}
