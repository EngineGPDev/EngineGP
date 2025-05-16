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

include(LIB . 'games/actions.php');

class action extends actions
{
    public static function start($id, $type = 'start')
    {
        global $cfg, $sql, $user, $start_point;

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `port`, `slots_start`, `name`, `ram`, `cpu`, `time_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        include(LIB . 'ssh.php');

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return ['e' => sys::text('error', 'ssh')];
        }

        $ip = $ssh->getInternalIp();
        $port = $server['port'];
        $server_address = $server['address'] . ':' . $server['port'];

        $serverSystemdStatus = trim($ssh->get('sudo systemctl show -p ActiveState server' . $server['uid'] . '.scope | awk -F \'=\' \'{print $2}\''));

        if ($serverSystemdStatus == 'failed') {
            $ssh->set('sudo systemctl stop server' . $server['uid'] . '.scope');
            $ssh->set('sudo systemctl reset-failed server' . $server['uid'] . '.scope');
        }

        // Убить процессы
        $ssh->set('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@' . $server_address . ' | awk ' . "'{print $2}'" . ' | grep -v PID | xargs`; sudo -u server' . $server['uid'] . ' tmux kill-session -t server' . $server['uid']);

        // Временный файл
        $temp = sys::temp(action::config($ip, $port, $server['slots_start'], $ssh->get('cat ' . $tarif['install'] . '/' . $server['uid'] . '/mods/deathmatch/mtaserver.conf')));

        // Обновление файла server.cfg
        $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/mods/deathmatch/mtaserver.conf');
        $ssh->set('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/mods/deathmatch/mtaserver.conf');

        unlink($temp);

        // Параметры запуска
        $bash = './mta-server';

        // Временный файл
        $temp = sys::temp($bash);

        // Обновление файла start.sh
        $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/start.sh');
        $ssh->set('chmod 0500' . ' ' . $tarif['install'] . $server['uid'] . '/start.sh');

        // Строка запуска
        $ssh->set('cd ' . $tarif['install'] . $server['uid'] . ';' // переход в директорию игрового сервера
            . 'rm *.pid;' // Удаление *.pid файлов
            . 'sudo -u server' . $server['uid'] . ' mkdir -p mods/deathmatch/oldstart;' // Создание папки логов
            . 'cat mods/deathmatch/logs/server.log >> mods/deathmatch/oldstart/' . date('d.m.Y_H:i:s', $server['time_start']) . '.log; rm mods/deathmatch/logs/server.log; rm mods/deathmatch/logs/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
            . 'chown server' . $server['uid'] . ':servers mods/deathmatch/mtaserver.conf start.sh;' // Обновление владельца файлов
            . 'sudo systemd-run --unit=server' . $server['uid'] . ' --scope -p CPUQuota=' . $server['cpu'] . '% -p MemoryMax=' . $server['ram'] . 'M sudo -u server' . $server['uid'] . ' tmux new-session -ds s_' . $server['uid'] . ' sh -c "./start.sh"'); // Запуск игровго сервера

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="' . $type . '", `online`="0", `players`="", `time_start`="' . $start_point . '", `stop`="1" WHERE `id`="' . $id . '" LIMIT 1');

        unlink($temp);

        // Сброс кеша
        actions::clmcache($id);

        sys::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0, 'players' => '']);
        sys::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0]);

        return ['s' => 'ok'];
    }

    public static function config($ip, $port, $slots, $config)
    {
        $aRepl = [
            '<serverip>', '</serverip>',
            '<maxplayers>', '</maxplayers>',
            '<httpserver>', '</httpserver>',
            '<serverport>', '</serverport>',
            '<httpport>', '</httpport>',
            '<logfile>', '</logfile>',
            '<ase>', '</ase>',
        ];

        $config = str_ireplace($aRepl, $aRepl, $config);
        $config = str_ireplace(['<config>', '</config>', "\r", "\t"], '', $config);
        $config = preg_replace("#\<\!--(.+?)--\>#is", '', $config);

        $aLine = explode("\n", $config);

        $search = [
            "#\<serverip\>(.+?)\<\/serverip\>#is",
            "#\<maxplayers\>(.+?)\<\/maxplayers\>#is",
            "#\<httpserver\>(.+?)\<\/httpserver\>#is",
            "#\<serverport\>(.+?)\<\/serverport\>#is",
            "#\<httpport\>(.+?)\<\/httpport\>#is",
            "#\<logfile\>(.+?)\<\/logfile\>#is",
            "#\<ase\>(.+?)\<\/ase\>#is",
        ];

        $config = '<config>' . PHP_EOL;

        foreach ($aLine as $line) {
            if (str_replace([' ', "\t"], '', $line) != '') {
                $edit = trim(preg_replace($search, ['', '', '', '', '', ''], $line));
            }

            if ($edit != '') {
                $config .= '    ' . $edit . PHP_EOL;
            }

            $edit = '';
        }

        $config .= '    <serverip>' . $ip . '</serverip>' . PHP_EOL
            . '    <maxplayers>' . $slots . '</maxplayers>' . PHP_EOL
            . '    <httpserver>1</httpserver>' . PHP_EOL
            . '    <serverport>' . $port . '</serverport>' . PHP_EOL
            . '    <httpport>' . ($port + 10000) . '</httpport>' . PHP_EOL
            . '    <ase>1</ase>' . PHP_EOL
            . '    <logfile>logs/server.log</logfile>';

        return $config . "\n" . '</config>';
    }
}
