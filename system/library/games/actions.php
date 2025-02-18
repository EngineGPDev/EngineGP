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
use EngineGP\Model\Parameters;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class actions
{
    public static function stop($id)
    {
        global $cfg, $sql, $user;

        include(LIB . 'ssh.php');

        $sql->query('SELECT `uid`, `unit`, `game`, `address`, `port`, `name` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return ['e' => System::text('error', 'ssh')];
        }

        $server_address = $server['address'] . ':' . $server['port'];

        $ssh->set('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@' . $server_address . ' | awk ' . "'{print $2}'" . ' | grep -v PID | xargs`; sudo -u server' . $server['uid'] . ' tmux kill-session -t server' . $server['uid']);

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="off", `online`="0", `players`="", `stop`="0" WHERE `id`="' . $id . '" LIMIT 1');

        // Сброс кеша
        actions::clmcache($id);

        System::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => '']);
        System::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0]);

        return ['s' => 'ok'];
    }

    public static function change($id, $map = false)
    {
        global $cfg, $sql, $html, $user, $mcache;

        // Если в кеше есть карты
        if ($mcache->get('server_maps_change_' . $id) != '' && !$map) {
            return ['maps' => $mcache->get('server_maps_change_' . $id)];
        }

        include(LIB . 'ssh.php');

        $sql->query('SELECT `uid`, `unit`, `game`, `tarif`, `online`, `players`, `name` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return ['e' => System::text('error', 'ssh')];
        }

        // Массив карт игрового сервера (папка "maps")
        $aMaps = explode("\n", $ssh->get('cd ' . $tarif['install'] . $server['uid'] . '/cstrike/maps/ && ls | grep .bsp | grep -v .bsp.'));

        // Удаление пустого элемента
        unset($aMaps[count($aMaps) - 1]);

        // Удаление ".bsp"
        $aMaps = str_replace('.bsp', '', $aMaps);

        // Если выбрана карта
        if ($map) {
            // Проверка наличия выбранной карты
            if (!in_array($map, $aMaps)) {
                return ['e' => System::updtext(System::text('servers', 'change'), ['map' => $map . '.bsp'])];
            }

            // Отправка команды changelevel
            $ssh->set('sudo -u server' . $server['uid'] . ' tmux send-keys -t s_' . $server['uid'] . ' "changelevel ' . System::cmd($map) . '" C-m');

            // Обновление информации в базе
            $sql->query('UPDATE `servers` set `status`="change" WHERE `id`="' . $id . '" LIMIT 1');

            // Сброс кеша
            actions::clmcache($id);

            System::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'change', 'online' => $server['online'], 'players' => base64_decode($server['players'])]);
            System::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'change', 'online' => $server['online']]);

            return ['s' => 'ok'];
        }

        // Сортировка списка карт
        sort($aMaps);
        reset($aMaps);

        // Генерация списка карт для выбора
        foreach ($aMaps as $map) {
            $html->get('change_list', 'sections/servers/games');
            $html->set('img', System::img($map, $server['game']));
            $html->set('name', $map);
            $html->set('id', $id);
            $html->pack('maps');
        }

        // Запись карт в кеш
        $mcache->set('server_maps_change_' . $id, $html->arr['maps'], false, 30);

        return ['maps' => $html->arr['maps']];
    }

    public static function reinstall($id)
    {
        global $cfg, $sql, $user, $start_point;

        include(LIB . 'ssh.php');

        $sql->query('SELECT `uid`, `unit`, `tarif`, `address`, `port`, `game`, `name`, `pack`, `plugins_use`, `ftp`, `reinstall` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        // Проверка времени переустановки
        $reinstall = $server['reinstall'] + $cfg['reinstall'][$server['game']] * 60;

        if ($reinstall > $start_point && $user['group'] != 'admin') {
            return ['e' => System::updtext(System::text('servers', 'reinstall'), ['time' => System::date('max', $reinstall)])];
        }

        $sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `path`, `install`, `plugins_install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return ['e' => System::text('error', 'ssh')];
        }

        $server_address = $server['address'] . ':' . $server['port'];

        $ssh->set('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@' . $server_address . ' | awk ' . "'{print $2}'" . ' | grep -v PID | xargs`; sudo -u server' . $server['uid'] . ' tmux kill-session -t server' . $server['uid']);

        // Директория сборки
        $path = $tarif['path'] . $server['pack'];

        // Директория игрового сервера
        $install = $tarif['install'] . $server['uid'];

        $ssh->set('rm -r ' . $install . ';' // Удаление директории игрового сервера
            . 'mkdir ' . $install . ';' // Создание директории
            . 'chown server' . $server['uid'] . ':servers ' . $install . ';' // Изменение владельца и группы директории
            . 'cd ' . $install . ' && sudo -u server' . $server['uid'] . ' tmux new-session -ds r_' . $server['uid'] . ' sh -c "'
            . 'cp -r ' . $path . '/. .;' // Копирование файлов сборки для сервера
            . 'find . -type d -exec chmod 700 {} \;;'
            . 'find . -type f -exec chmod 600 {} \;;'
            . 'chmod 500 ' . Parameters::$aFileGame[$server['game']] . '"');

        // Очистка записей в базе
        $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '"'); // Список админов на сервере
        $sql->query('DELETE FROM `plugins_install` WHERE `server`="' . $id . '"'); // Список установленных плагинов на сервере

        // Запись установленных плагинов
        if ($server['plugins_use']) {
            // Массив идентификаторов плагинов
            $aPlugins = System::b64djs($tarif['plugins_install']);

            if (isset($aPlugins[$server['pack']])) {
                $plugins = explode(',', $aPlugins[$server['pack']]);

                foreach ($plugins as $plugin) {
                    if ($plugin) {
                        $sql->query('INSERT INTO `plugins_install` set `server`="' . $id . '", `plugin`="' . $plugin . '", `time`="' . $start_point . '"');
                    }
                }
            }
        }

        // Обновление информации в базе
        $sql->query('UPDATE `servers` set `status`="reinstall", `reinstall`="' . $start_point . '", `fastdl`="0" WHERE `id`="' . $id . '" LIMIT 1');

        // Логирование
        $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . System::text('syslogs', 'reinstall') . '", `time`="' . $start_point . '"');

        // Сброс кеша
        actions::clmcache($id);

        System::reset_mcache('server_scan_mon_pl_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'reinstall', 'online' => 0, 'players' => '']);
        System::reset_mcache('server_scan_mon_' . $id, $id, ['name' => $server['name'], 'game' => $server['game'], 'status' => 'reinstall', 'online' => 0]);

        return ['s' => 'ok'];
    }

    public static function update($id)
    {
        global $cfg, $sql, $user, $start_point;

        include(LIB . 'ssh.php');

        $sql->query('SELECT `uid`, `unit`, `tarif`, `address`, `port`, `game`, `name`, `pack`, `plugins_use`, `ftp`, `update` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        $server = $sql->get();

        // Проверка времени обновления
        $update = $server['update'] + $cfg['update'][$server['game']] * 60;

        if ($update > $start_point && $user['group'] != 'admin') {
            return ['e' => System::updtext(System::text('servers', 'update'), ['time' => System::date('max', $update)])];
        }

        $sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `update`, `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Проверка ssh соедниения пу с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return ['e' => System::text('error', 'ssh')];
        }

        $server_address = $server['address'] . ':' . $server['port'];

        $ssh->set('kill -9 `ps aux | grep s_' . $server['uid'] . ' | grep -v grep | awk ' . "'{print $2}'" . ' | xargs;'
            . 'lsof -i@' . $server_address . ' | awk ' . "'{print $2}'" . ' | grep -v PID | xargs`; sudo -u server' . $server['uid'] . ' tmux kill-session -t server' . $server['uid']);

        // Директория обновлений сборки
        $path = $tarif['update'] . $server['pack'];

        // Директория игрового сервера
        $install = $tarif['install'] . $server['uid'];

        $ssh->set('cd ' . $install . ' && sudo -u server' . $server['uid'] . ' tmux new-session -ds u_' . $server['uid'] . ' sh -c "cp -rv ' . $path . '/. .;' // Копирование файлов обвновления сборки для сервера
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

        return ['s' => 'ok'];
    }

    public static function clmcache($id)
    {
        global $mcache;

        $mcache->delete('server_index_' . $id);
        $mcache->delete('server_resources_' . $id);
        $mcache->delete('server_status_' . $id);
    }
}
