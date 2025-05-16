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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class notice_help_admin extends cron
{
    public function __construct()
    {
        global $cfg, $sql;

        $sql->query('SELECT `id`, `time`, `notice_admin` FROM `help` WHERE (`notice_admin`="0" OR `notice_admin`="2") AND `close`="0" LIMIT 1');
        if (!$sql->num()) {
            return null;
        }

        $help = $sql->get();

        foreach ($cfg['notice_admin'] as $id) {
            $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $id . '" LIMIT 1');
            $admin = $sql->get();

            if ($help['notice_admin'] != 2) {
                if (!sys::mail('Техническая поддержка', sys::updtext(sys::text('mail', 'notice_help_admin_new'), ['url' => $cfg['http'] . 'help/section/dialog/id/' . $help['id']]), $admin['mail'])) {
                    continue;
                }
            } else {
                if (!sys::mail('Техническая поддержка', sys::updtext(sys::text('mail', 'notice_help_admin'), ['url' => $cfg['http'] . 'help/section/dialog/id/' . $help['id']]), $admin['mail'])) {
                    continue;
                }
            }
        }

        $sql->query('UPDATE `help` set `notice_admin`="1" WHERE `id`="' . $help['id'] . '" LIMIT 1');

        return null;
    }
}
