<?php

/*
 * Copyright 2018-2024 Solovev Sergei
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

class preparing_web_delete extends cron
{
    public function __construct()
    {
        global $argv, $cfg, $sql;

        $sql->query('SELECT `id` FROM `web` WHERE `user`="0"');
        while ($web = $sql->get()) {
            exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' web_delete ' . $web['id'] . '"');
        }

        return null;
    }
}
