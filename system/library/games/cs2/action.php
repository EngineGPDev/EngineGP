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
use EngineGP\Model\Game;
use EngineGP\Model\Parameters;
use EngineGP\Infrastructure\RemoteAccess\SshClient;
use EngineGP\Infrastructure\RemoteAccess\SftpClient;
use EngineGP\Infrastructure\Network\InternalIpFetcher;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class action extends actions
{
    public static function start($id, $type = 'start')
    {
        global $cfg, $sql, $user, $start_point;

        $sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `port`, `slots_start`, `name`, `tickrate`, `ram`, `map_start`, `vac`, `time_start`, `pingboost`, `cpu` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
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
            $createLinkCommand = 'mkdir -p ' . $tarif['install'] . $server['uid'] . '/.steam/sdk64/' . ';'
                . 'ln -s ' . $cfg['steamcmd'] . '/linux64/steamclient.so ' . $tarif['install'] . $server['uid'] . '/.steam/sdk64/' . ';'
                . 'chown -R server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/.steam' . ';'
                . 'find ' . $tarif['install'] . $server['uid'] . '/.steam' . ' -type d -exec chmod 700 {} \;';
            $sshClient->execute($createLinkCommand);
        }

        $sshClient->execute('chmod +x ' . $tarif['install'] . $server['uid'] . '/game/bin/linuxsteamrt64/cs2');

        // Проверка наличия стартовой карты
        $output = $sshClient->execute('cd ' . $tarif['install'] . $server['uid'] . '/game/csgo/maps/ && du -ah | grep -e "\.vpk$" | awk \'{print $2}\'', false);

        if (Game::map($server['map_start'], $output)) {
            return ['e' => System::updtext(System::text('servers', 'nomap'), ['map' => $server['map_start'] . '.vpk'])];
        }

        // Античит VAC
        $vac = $server['vac'] == 0 ? '-insecure' : '-secure';

        // Боты
        $bots = $cfg['bots'][$server['game']] ? '' : '-nobots';

        // TV
        $tv = isset($server['tv']) ? '+tv_enable 1 +tv_maxclients 30 +tv_port ' . ($port + 10000) : '-nohltv';

        $check = explode('/', $server['map_start']);

        // Стартовая карта
        $map = $check[0] == 'workshop' ? '+workshop_start_map ' . $check[1] : '+map \'' . $server['map_start'] . '\'';

        // Игровой режим
        $mods = [
            1 => '+game_type 0 +game_mode 0',
            2 => '+game_type 0 +game_mode 1',
            3 => '+game_type 1 +game_mode 0',
            4 => '+game_type 1 +game_mode 1',
            5 => '+game_type 1 +game_mode 2',
        ];

        $mod = !$server['pingboost'] ? $mods[2] : $mods[$server['pingboost']];

        // Параметры запуска
        $bash = './game/bin/linuxsteamrt64/cs2 -dedicated -debug -norestart -condebug console.log -usercon -ip ' . $ip . ' -port ' . $port . ' -maxplayers ' . $server['slots_start'] . ' -tickrate ' . $server['tickrate'] . ' ' . $mod . ' +servercfgfile server.cfg ' . $map . ' ' . $vac . ' ' . $bots . ' ' . $tv;

        // Временный файл
        $temp = System::temp($bash);

        // Обновление файла start.sh
        $sftpClient->putFile($temp, $tarif['install'] . $server['uid'] . '/start.sh');
        $sshClient->execute('chmod 0500' . ' ' . $tarif['install'] . $server['uid'] . '/start.sh');

        // Строка запуска
        $sshClient->execute('cd ' . $tarif['install'] . $server['uid'] . ';' // переход в директорию игрового сервера
            . 'rm *.pid;' // Удаление *.pid файлов
            . 'sudo -u server' . $server['uid'] . ' mkdir -p game/csgo/oldstart;' // Создание папки логов
            . 'cat game/csgo/console.log >> game/csgo/oldstart/' . date('d.m.Y_H:i:s', $server['time_start']) . '.log; rm game/csgo/console.log; rm game/csgo/oldstart/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
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

    public static function change($id, $map = false)
    {
        global $cfg, $sql, $html, $user, $mcache;

        // Если в кеше есть карты
        if ($mcache->get('server_maps_change_' . $id) != '' and !$map) {
            return ['maps' => $mcache->get('server_maps_change_' . $id)];
        }

        $sql->query('SELECT `uid`, `unit`, `game`, `tarif`, `online`, `players`, `name` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $sshClient = new SshClient($unit['address'], 'root', $unit['passwd']);

        // Массив карт игрового сервера (папка "maps")
        $aMaps = explode("\n", $sshClient->execute('cd ' . $tarif['install'] . $server['uid'] . '/game/csgo/maps/ && du -ah | grep -e "\.vpk$" | awk \'{print $2}\'', false));

        // Удаление пустого элемента
        unset($aMaps[count($aMaps) - 1]);

        // Удаление ".vpk"
        $aMaps = str_ireplace(['./', '.vpk'], '', $aMaps);

        // Если выбрана карта
        if ($map) {
            $map = str_replace('|', '/', $map);

            // Проверка наличия выбранной карты
            if (Game::map($map, $aMaps)) {
                return ['e' => System::updtext(System::text('servers', 'change'), ['map' => $map . '.vpk'])];
            }

            // Отправка команды changelevel
            $sshClient->execute('sudo -u server' . $server['uid'] . ' tmux send-keys -t s_' . $server['uid'] . ' "changelevel ' . System::cmd($map) . '" C-m');

            // Обновление информации в базе
            $sql->query('UPDATE `servers` set `status`="change" WHERE `id`="' . $id . '" LIMIT 1');

            // Сброс кеша
            actions::clmcache($id);

            System::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'change', 'online' => $server['online'], 'players' => base64_decode($server['players'])]);
            System::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'change', 'online' => $server['online']]);

            $sshClient->disconnect();

            return ['s' => 'ok'];
        }

        // Сортировка списка карт
        sort($aMaps);
        reset($aMaps);

        // Генерация списка карт для выбора
        foreach ($aMaps as $map) {
            $aName = explode('/', $map);
            $name = end($aName);

            $html->get('change_list', 'sections/servers/cs2');

            $html->set('img', file_exists(DIR . '/maps/' . $server['game'] . '/' . $name . '.jpg') ? $cfg['http'] . 'maps/' . $server['game'] . '/' . $name . '.jpg' : $cfg['http'] . 'template/images/status/none.jpg');
            $html->set('map', str_replace('/', '|', $map));
            $html->set('name', $name);
            $html->set('id', $id);

            if (count($aName) > 1) {
                $html->unit('workshop', true);
            } else {
                $html->unit('workshop');
            }

            $html->pack('maps');
        }

        // Запись карт в кеш
        $mcache->set('server_maps_change_' . $id, $html->arr['maps'], false, 60);

        return ['maps' => $html->arr['maps']];
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

        $sshClient->execute('cd ' . $cfg['steamcmd'] . ' && ' . 'tmux new-session -ds u_' . $server['uid'] . ' sh -c "'
            . './steamcmd.sh +login anonymous +force_install_dir "' . $install . '" +app_update 730 +quit;'
            . 'cd ' . $install . ';'
            . 'chown -R server' . $server['uid'] . ':servers .;'
            . 'find . -type d -exec chmod 700 {} \;;'
            . 'find . -type f -exec chmod 600 {} \;;'
            . 'chmod 500 ' . Parameters::$aFileGame[$server['game']] . '"');

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
