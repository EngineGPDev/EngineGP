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

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `port`, `slots_start`, `name`, `tickrate`, `ram`, `map_start`, `vac`, `cpu` , `time_start` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
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

        // Проверка наличия .steam
        $checkLinkCommand = 'ls -la' . $tarif['install'] . $server['uid'];
        $checkLinkOutput = $sshClient->execute($checkLinkCommand, false);

        // Если .steam отсуствует, создаём каталог и символическую ссылку на steamclient.so
        if (strpos($checkLinkOutput, '.steam') === false) {
            $createLinkCommand = 'mkdir -p ' . $tarif['install'] . $server['uid'] . '/.steam/sdk32/' . ';'
                . 'ln -s ' . $cfg['steamcmd'] . '/linux32/steamclient.so ' . $tarif['install'] . $server['uid'] . '/.steam/sdk32/' . ';'
                . 'chown -R server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/.steam' . ';'
                . 'find ' . $tarif['install'] . $server['uid'] . '/.steam' . ' -type d -exec chmod 700 {} \;';
            $sshClient->execute($createLinkCommand);
        }

        // Проверка наличия стартовой карты
        $output = $sshClient->execute('cd ' . $tarif['install'] . $server['uid'] . '/cstrike/maps/ && ls | grep .bsp | grep -v .bsp.', false);

        if ($server['map_start'] != '' and !in_array($server['map_start'], str_replace('.bsp', '', explode("\n", $output)))) {
            return ['e' => System::updtext(System::text('servers', 'nomap'), ['map' => $server['map_start'] . '.bsp'])];
        }

        // Античит VAC
        $vac = $server['vac'] == 0 ? '-insecure' : '-secure';

        // Боты
        $bots = $cfg['bots'][$server['game']] ? '' : '-nobots';

        // TV
        $tv = isset($server['tv']) ? '+tv_enable 1 +tv_maxclients 30 +tv_port ' . ($port + 10000) : '-nohltv';

        // Параметры запуска
        $bash = './srcds_run -debug -game cstrike -norestart -condebug console.log -tickrate ' . $server['tickrate'] . '  +servercfgfile server.cfg +map \'' . $server['map_start'] . '\' +maxplayers ' . $server['slots_start'] . ' +ip ' . $ip . ' +port ' . $port . ' -sv_lan 0 ' . $vac . ' ' . $bots . ' ' . $tv;

        // Временный файл
        $temp = System::temp($bash);

        // Обновление файла start.sh
        $sftpClient->putFile($temp, $tarif['install'] . $server['uid'] . '/start.sh');
        $sshClient->execute('chmod 0500' . ' ' . $tarif['install'] . $server['uid'] . '/start.sh');

        // Строка запуска
        $sshClient->execute('cd ' . $tarif['install'] . $server['uid'] . ';' // переход в директорию игрового сервера
            . 'rm *.pid;' // Удаление *.pid файлов
            . 'sudo -u server' . $server['uid'] . ' mkdir -p cstrike/oldstart;' // Создание папки логов
            . 'cat cstrike/console.log >> cstrike/oldstart/' . date('d.m.Y_H:i:s', $server['time_start']) . '.log; rm cstrike/console.log; rm cstrike/oldstart/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
            . 'chown server' . $server['uid'] . ':servers start.sh;' // Обновление владельца файла start.sh
            . 'sudo systemd-run --unit=server' . $server['uid'] . ' --scope -p CPUQuota=' . $server['cpu'] . '% -p MemoryMax=' . $server['ram'] . 'M sudo -u server' . $server['uid'] . ' tmux new-session -ds s_' . $server['uid'] . ' sh -c "./start.sh"'); // Запуск игровго сервера

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

    public static function update($id)
    {
        global $cfg, $sql, $user, $start_point;

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `name`, `ftp`, `update` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        // Проверка времени обновления
        $update = $server['update'] + $cfg['update'][$server['game']] * 60;

        if ($update > $start_point and $user['group'] != 'admin') {
            return ['e' => System::updtext(System::text('servers', 'update'), ['time' => System::date('max', $update)])];
        }

        $sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `install`, `plugins_install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $sshClient = new SshClient($unit['address'], 'root', $unit['passwd']);

        // Директория игрового сервера
        $install = $tarif['install'] . $server['uid'];

        $sshClient->execute('cd ' . $cfg['steamcmd'] . ' && sudo -u server' . $server['uid'] . ' sh -c "tmux new-session -ds u_' . $server['uid'] . ' ./steamcmd.sh +login anonymous +force_install_dir "' . $install . '" +app_update 232330 +quit"');

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="update", `update`="' . $start_point . '" WHERE `id`="' . $id . '" LIMIT 1');

        // Логирование
        $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . System::text('syslogs', 'update') . '", `time`="' . $start_point . '"');

        // Сброс кеша
        actions::clmcache($id);

        System::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'update', 'online' => 0, 'players' => '']);
        System::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'update', 'online' => 0]);

        $sshClient->disconnect();

        return ['s' => 'ok'];
    }
}
