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

class server_cron extends cron
{
    public function __construct()
    {
        global $argv, $sql, $cfg;

        $sql->query('SELECT `game` FROM `servers` WHERE `id`="' . $argv[3] . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `task` FROM `crontab` WHERE `id`="' . $argv[4] . '" LIMIT 1');
        $cron = $sql->get();

        $cmd = $cron['task'] == 'console' ? ' ' . $argv[4] : '';

        exec('sh -c "cd /var/www/enginegp; php cron.php ' . $cfg['cron_key'] . ' server_action ' . $cron['task'] . ' ' . $server['game'] . ' ' . $argv[3] . $cmd . '"');

        return null;
    }
}
