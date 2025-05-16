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

use EngineGP\AdminSystem;
use EngineGP\Infrastructure\RemoteAccess\SshClient;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if (isset($url['delete']) and $url['delete'] == 'all') {
    $sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
    $panel = $sql->get();

    $sshClient = new SshClient($panel['address'], 'root', $panel['passwd']);

    try {
        $sshClient->connect();
    } catch (\Exception $e) {
        AdminSystem::outjs(['e' => 'PANEL не удалось создать связь.'], false);
    }

    $servers = $sql->query('SELECT `id`, `user`, `game` FROM `servers` WHERE `unit`="' . $id . '"');
    while ($server = $sql->get($servers)) {
        $crons = $sql->query('SELECT `id`, `cron` FROM `crontab` WHERE `server`="' . $server['id'] . '"');
        while ($cron = $sql->get($crons)) {
            $crontab = preg_quote($cron['cron'], '/');

            $sshClient->execute('crontab -l | grep -v "' . $crontab . '" | crontab -', true);

            $sql->query('DELETE FROM `crontab` WHERE `id`="' . $cron['id'] . '" LIMIT 1');
        }

        $helps = $sql->query('SELECT `id` FROM `help` WHERE `type`="server" AND `service`="' . $server['id'] . '"');
        while ($help = $sql->get($helps)) {
            $sql->query('DELETE FROM `help_dialogs` WHERE `help`="' . $help['id'] . '"');
            $sql->query('DELETE FROM `help` WHERE `id`="' . $help['id'] . '" LIMIT 1');
        }

        $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $server['id'] . '" LIMIT 1');
        $sql->query('DELETE FROM `address_buy` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `logs_sys` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `owners` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `copy` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `plugins_install` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `graph` WHERE `server`="' . $server['id'] . '" LIMIT 1');
        $sql->query('DELETE FROM `graph_day` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `graph_hour` WHERE `server`="' . $server['id'] . '"');
        $sql->query('DELETE FROM `web` WHERE `server`="' . $server['id'] . '"');
    }

    $sql->query('DELETE FROM `address` WHERE `unit`="' . $id . '"');
    $sql->query('DELETE FROM `tarifs` WHERE `unit`="' . $id . '"');
} else {
    $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $id . '" LIMIT 1');
    if ($sql->num()) {
        AdminSystem::outjs(['e' => 'Нельзя удалить локацию с серверами.']);
    }
}

$sql->query('DELETE FROM `units` WHERE `id`="' . $id . '" LIMIT 1');

AdminSystem::outjs(['s' => 'ok']);
