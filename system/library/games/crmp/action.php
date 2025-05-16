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
use EngineGP\Infrastructure\RemoteAccess\SshClient;
use EngineGP\Infrastructure\RemoteAccess\SftpClient;
use EngineGP\Infrastructure\Network\InternalIpFetcher;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

include(LIB . 'games/actions.php');

class action extends actions
{
    public static function start($id, $type = 'start')
    {
        global $cfg, $sql, $user, $start_point;

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `port`, `slots_start`, `name`, `ram`, `map_start`, `cpu`, `time_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sshClient = new SshClient($unit['address'], 'root', $unit['passwd']);
        $sftpClient = new SftpClient($unit['address'], 'root', $unit['passwd']);
        $internalIpFetcher = new InternalIpFetcher($sshClient);

        $ip = $internalIpFetcher->getInternalIp();
        $port = $server['port'];
        $server_address = $server['address'] . ':' . $server['port'];

        $serverSystemdStatus = trim($sshClient->execute('sudo systemctl show -p ActiveState server' . $server['uid'] . '.scope | awk -F \'=\' \'{print $2}\'', false));

        if ($serverSystemdStatus == 'failed') {
            $sshClient->execute('sudo systemctl stop server' . $server['uid'] . '.scope');
            $sshClient->execute('sudo systemctl reset-failed server' . $server['uid'] . '.scope');
        }

        // Убить процессы
        $sshClient->execute('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@' . $server_address . ' | awk ' . "'{print $2}'" . ' | grep -v PID | xargs`; sudo -u server' . $server['uid'] . ' tmux kill-session -t server' . $server['uid']);

        // Временный файл
        $temp = System::temp(action::config($ip, $port, $server['slots_start'], $sshClient->execute('cat ' . $tarif['install'] . '/' . $server['uid'] . '/server.cfg', false)));

        // Обновление файла server.cfg
        $sftpClient->putFile($temp, $tarif['install'] . $server['uid'] . '/server.cfg');
        $sshClient->execute('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/server.cfg');

        unlink($temp);

        // Параметры запуска
        $bash = './samp03svr-cr';

        // Временный файл
        $temp = System::temp($bash);

        // Обновление файла start.sh
        $sftpClient->putFile($temp, $tarif['install'] . $server['uid'] . '/start.sh');
        $sshClient->execute('chmod 0500' . ' ' . $tarif['install'] . $server['uid'] . '/start.sh');

        // Строка запуска
        $sshClient->execute('cd ' . $tarif['install'] . $server['uid'] . ';' // переход в директорию игрового сервера
            . 'rm *.pid;' // Удаление *.pid файлов
            . 'sudo -u server' . $server['uid'] . ' mkdir -p oldstart;' // Создание папки логов
            . 'cat server_log.txt >> oldstart/' . date('d.m.Y_H:i:s', $server['time_start']) . '.log; rm server_log.txt; rm oldstart/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
            . 'chown server' . $server['uid'] . ':servers server.cfg start.sh;' // Обновление владельца файлов server.cfg start.sh
            . 'sudo systemd-run --unit=server' . $server['uid'] . ' --scope -p CPUQuota=' . $server['cpu'] . '% -p MemoryMax=' . $server['ram'] . 'M sudo -u server' . $server['uid'] . ' tmux new-session -ds s_' . $server['uid'] . ' sh -c ./start.sh'); // Запуск игрового сервера

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="' . $type . '", `online`="0", `players`="", `time_start`="' . $start_point . '", `stop`="1" WHERE `id`="' . $id . '" LIMIT 1');

        unlink($temp);

        // Сброс кеша
        actions::clmcache($id);

        System::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0, 'players' => '']);
        System::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0]);

        $sshClient->disconnect();
        $sftpClient->disconnect();

        return ['s' => 'ok'];
    }

    public static function config($ip, $port, $slots, $config)
    {
        $aLine = explode("\n", $config);

        $eConfig = '';

        foreach ($aLine as $line) {
            $param = explode(' ', trim($line));

            if (in_array(trim($param[0]), ['bind', 'port', 'maxplayers', 'query'])) {
                continue;
            }

            $eConfig .= $line . PHP_EOL;
        }

        $eConfig .= 'bind ' . $ip . PHP_EOL
            . 'port ' . $port . PHP_EOL
            . 'maxplayers ' . $slots . PHP_EOL
            . 'query 1';

        return $eConfig;
    }
}
