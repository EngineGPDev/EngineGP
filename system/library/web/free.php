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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class web
{
    public static function install($aData = [], $mcache)
    {
        global $cfg, $sql, $start_point;

        include(DATA . 'web.php');

        if (!$aWeb[$aData['server']['game']][$aData['type']]) {
            System::outjs(['e' => 'Дополнительная услуга недоступна для установки.'], $mcache);
        }

        // Проверка на наличие уже установленной выбранной услуги
        if ($sql->num(web::stack($aData, '`id`'))) {
            System::outjs(['i' => 'Дополнительная услуга уже установлена.'], $mcache);
        }

        // Проверка на наличие уже установленной подобной услуги
        switch ($aWebInstall[$aData['server']['game']][$aData['type']]) {
            case 'server':
                foreach ($aWebOne[$aData['server']['game']][$aData['type']] as $type) {
                    $sql->query('SELECT `id` FROM `web` WHERE `type`="' . $type . '" AND `server`="' . $aData['server']['id'] . '" LIMIT 1');
                    if ($sql->num()) {
                        System::outjs(['i' => 'Подобная услуга уже установлена.', 'type' => $type], $mcache);
                    }
                }

                break;

            case 'user':
                foreach ($aWebOne[$aData['server']['game']][$aData['type']] as $type) {
                    $sql->query('SELECT `id` FROM `web` WHERE `type`="' . $type . '" AND `user`="' . $aData['server']['user'] . '" LIMIT 1');
                    if ($sql->num()) {
                        System::outjs(['i' => 'Подобная услуга уже установлена.', 'type' => $type], $mcache);
                    }
                }

                break;

            case 'unit':
                foreach ($aWebOne[$aData['server']['game']][$aData['type']] as $type) {
                    $sql->query('SELECT `id` FROM `web` WHERE `type`="' . $type . '" AND `user`="' . $aData['server']['user'] . '" AND `unit`="' . $aData['server']['unit'] . '" LIMIT 1');
                    if ($sql->num()) {
                        System::outjs(['i' => 'Подобная услуга уже установлена.', 'type' => $type], $mcache);
                    }
                }
        }

        // Проверка валидности поддомена
        if (System::valid($aData['subdomain'], 'other', "/^[a-z0-9]+$/")) {
            System::outjs(['e' => 'Адрес должен состоять из букв a-z и цифр.'], $mcache);
        }

        // Проверка длины поддомена
        if (!isset($aData['subdomain'][3]) || isset($aData['subdomain'][15])) {
            System::outjs(['e' => 'Длина адреса не должна превышать 16-и символов и быть не менее 4-х символов.'], $mcache);
        }

        // Проверка запрещенного поддомена
        if (in_array($aData['subdomain'], $aWebUnit['subdomains'])) {
            System::outjs(['e' => 'Нельзя создать данный адрес, придумайте другой.'], $mcache);
        }

        // Проверка наличия домена
        if (!in_array($aData['domain'], $aWebUnit['domains'])) {
            System::outjs(['e' => 'Выбранный домен не найден.'], $mcache);
        }

        // Проверка поддомена на занятость
        $sql->query('SELECT `id` FROM `web` WHERE `domain`="' . $aData['subdomain'] . '.' . $aData['domain'] . '" LIMIT 1');
        if ($sql->num()) {
            System::outjs(['e' => 'Данный адрес уже занят.'], $mcache);
        }

        // Проверка наличия шаблона
        if (!array_key_exists($aData['desing'], $aWebParam[$aData['type']]['desing'])) {
            System::outjs(['e' => 'Выбранный шаблон не найден.'], $mcache);
        }

        if (isset($aData['passwd'])) {
            // Если не указан пароль сгенерировать
            if ($aData['passwd'] == '') {
                $aData['passwd'] = System::passwd($aWebParam[$aData['type']]['passwd']);
            }

            // Проверка длинны пароля
            if (!isset($aData['passwd'][5]) || isset($aData['passwd'][5])) {
                System::outjs(['e' => 'Необходимо указать пароль длинной не менее 6-и символов и не более 16-и.'], $mcache);
            }

            // Проверка валидности пароля
            if (System::valid($aData['passwd'], 'other', "/^[A-Za-z0-9]{6,16}$/")) {
                System::outjs(['e' => 'Пароль должен состоять из букв a-z и цифр.'], $mcache);
            }
        }

        include(LIB . 'ssh.php');

        $unit = web::unit($aWebUnit, $aData['type'], $aData['server']['unit']);

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')], $mcache);
        }

        // Директория файлов услуги
        $path = $aWebUnit['path'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $aData['desing'];

        // Директория дополнительной услуги
        $install = $aWebUnit['install'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $aData['subdomain'] . '.' . $aData['domain'];

        $sql->query('INSERT INTO `web` set `type`="' . $aData['type'] . '", `server`="' . $aData['server']['id'] . '", `user`="' . $aData['server']['user'] . '", `unit`="' . $aData['server']['unit'] . '", `config`=""');
        $wid = $sql->id();
        $uid = $wid + 10000;

        $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $aData['server']['user'] . '" LIMIT 1');
        if (!$sql->num()) {
            $sql->query('DELETE FROM `web` WHERE `id`="' . $wid . '" LIMIT 1');

            System::outjs(['e' => 'Необходимо указать пользователя сервера.'], $mcache);
        }

        $u = $sql->get();

        // Данные
        $login = 'w' . $uid;
        $passwd = System::passwd(10);
        $ip = System::first(explode(':', $unit['address']));
        $host = $aWebUnit['unit'][$aData['type']] == 'local' ? '127.0.0.1' : $ip;

        $conf = [
            'address' => $aData['server']['address'],
            'install' => $install,
            'login' => $login,
            'passwd' => $passwd,
            'host' => $host,
            'url' => $cfg['http'],
            'domain' => $aData['subdomain'] . '.' . $aData['domain'],
        ];

        $aData['config_sql'] = System::updtext($aData['config_sql'], $conf);

        if (isset($aWebdbConf[$aData['type']])) {
            $aData['config_php'] = System::updtext($aData['config_php'], $conf);

            $temp = System::temp($aData['config_php']);
            $ssh->setfile($temp, $path . $aWebdbConf[$aData['type']]['file']);
            $ssh->set('chmod ' . $aWebdbConf[$aData['type']]['chmod'] . ' ' . $path . $aWebdbConf[$aData['type']]['file']);

            unlink($temp);
        }

        if (isset($aWebothPath[$aData['type']])) {
            $aData['config_oth'] = System::updtext($aData['config_oth'], $conf);

            $temp = System::temp($aData['config_oth']);
            $ssh->setfile($temp, $path . $aWebothPath[$aData['type']]['file']);
            $ssh->set('chmod ' . $aWebothPath[$aData['type']]['chmod'] . ' ' . $aWebothPath[$aData['type']]['file']);

            unlink($temp);
        }

        // Создание поддомена
        $result = json_decode(file_get_contents(System::updtext($aWebUnit['isp']['domain']['create'], ['subdomain' => $aData['subdomain'], 'ip' => $ip, 'domain' => $aData['domain']])), true);
        if (!isset($result['result']) || strtolower($result['result']) != 'ok') {
            $sql->query('DELETE FROM `web` WHERE `id`="' . $wid . '" LIMIT 1');

            System::outjs(['e' => 'Не удалось создать поддомен, обратитесь в тех.поддержку.'], $mcache);
        }

        // Создание задания crontab
        if (isset($aWebUnit['isp']['crontab'][$aData['type']]['install'])) {
            $result = json_decode(file_get_contents(System::updtext($aWebUnit['isp']['crontab'][$aData['type']]['install'], ['subdomain' => $aData['subdomain'], 'domain' => $aData['domain']])), true);
            if (!isset($result['result']) || strtolower($result['result']) != 'ok') {
                $sql->query('DELETE FROM `web` WHERE `id`="' . $wid . '" LIMIT 1');

                System::outjs(['e' => 'Не удалось создать задание, обратитесь в тех.поддержку.'], $mcache);
            }
        }

        $a2 = '<VirtualHost ' . $ip . ':80>' . PHP_EOL
            . '    ServerName ' . $aData['subdomain'] . '.' . $aData['domain'] . PHP_EOL
            . '    DocumentRoot ' . $install . PHP_EOL
            . '    AddType application/x-httpd-php .php .php3 .php4 .php5 .phtml' . PHP_EOL
            . '    AddType application/x-httpd-php-source .phps' . PHP_EOL
            . '</VirtualHost>';

        // Смена прав на файлы/папки
        $chmod = $aWebChmod[$aData['type']] ?? '';

        $sql_q = '';

        if (isset($aWebSQL[$aData['type']])) {
            $sql_q .= 'mysql --login-path=local -e "CREATE DATABASE ' . $login . ';'
                . "CREATE USER '" . $login . "'@'%' IDENTIFIED BY '" . $passwd . "';"
                . 'GRANT ALL PRIVILEGES ON ' . $login . ' . * TO \'' . $login . '\'@\'%\';";'
                . 'mysql --login-path=local ' . $login . ' < ' . $aWebUnit['path'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . 'dump.sql;';

            if (isset($aWebSQL[$aData['type']]['install'])) {
                foreach ($aWebSQL[$aData['type']]['install'] as $query) {
                    $sql_q .= "mysql --login-path=local " . $login . " -e \"" . System::updtext(
                        $query,
                        [
                                'url' => $cfg['http'],
                                'passwd' => $aData['passwd'],
                                'mail' => $u['mail'],
                                'folder' => $install]
                    ) . "\";";
                }
            }
        }

        // Установка
        $ssh->set('echo "' . $a2 . '" > /etc/apache2/sites-enabled/' . $aData['subdomain'] . '.' . $aData['domain'] . ';' // Настроки апач
            . 'mkdir -p ' . $install . ';' // Создание директории
            . 'useradd -d ' . $install . ' -g web -u ' . $uid . ' web' . $uid . ';' // Создание пользователя услуги на локации
            . 'chown -R web' . $uid . ':999 ' . $install . ';' // Изменение владельца и группы директории
            . 'cd ' . $install . ' && sudo -u web' . $uid . ' tmux new-session -ds i_w_' . $uid . ' sh -c "cp -r ' . $path . '/. .; ' . $chmod . '";' // Копирование файлов услуги
            . 'tmux new-session -ds apache_reload_' . $uid . ' service apache2 reload;' // Перезагрузить конфигурации апач
            . $sql_q); // sql запросы

        $aData['passwd'] ??= '';

        // Обновление данных
        $sql->query('UPDATE `web` set `uid`="' . $uid . '", `desing`="' . $aData['desing'] . '", '
            . '`domain`="' . $aData['subdomain'] . '.' . $aData['domain'] . '", '
            . '`passwd`="' . $aData['passwd'] . '", `config`="' . base64_encode($aData['config_sql']) . '", '
            . '`login`="' . $login . '", `date`="' . $start_point . '" '
            . 'WHERE `id`="' . $wid . '" LIMIT 1');

        System::outjs(['s' => 'ok'], $mcache);
    }

    public static function update($aData = [], $mcache)
    {
        global $sql, $start_point;

        include(DATA . 'web.php');

        $stack = web::stack($aData, '`id`, `uid`, `unit`, `login`, `desing`, `domain`, `update`');

        if (!$sql->num($stack)) {
            System::outjs(['e' => 'Дополнительная услуга не установлена.'], $mcache);
        }

        $web = $sql->get($stack);

        // Проверка времени последнего обновления
        $upd = $web['update'] + 86400;

        if ($upd > $start_point) {
            System::outjs(['e' => 'Для повторного обновления должно пройти: ' . Game::date('max', $upd)]);
        }

        include(LIB . 'ssh.php');

        $unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')], $mcache);
        }

        $install = $aWebUnit['install'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $web['domain'];

        $path = $aWebUnit['path'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $web['desing'];

        $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . $aData['server']['user'] . '" LIMIT 1');
        if (!$sql->num()) {
            System::outjs(['e' => 'Необходимо указать пользователя сервера.'], $mcache);
        }

        $u = $sql->get();

        // sql запросы
        $sql_q = '';

        if (isset($aWebSQL[$aData['type']]['update'])) {
            foreach ($aWebSQL[$aData['type']]['update'] as $query) {
                $sql_q .= "mysql --login-path=local " . $web['login'] . " -e \"" . System::updtext($query, ['passwd' => $aData['passwd'], 'mail' => $u['mail']]) . "\";";
            }
        }

        $cat = isset($aWebdbConf[$aData['type']]) ? 'cat ' . $install . $aWebdbConf[$aData['type']]['file'] . ' > ' . $path . $aWebdbConf[$aData['type']]['file'] . ';' : '';
        $chmod = $aWebChmod[$aData['type']] ?? '';

        $ssh->set($cat
            . 'cd ' . $install . ' && sudo -u web' . $web['uid'] . ' tmux new-session -ds u_w_' . $web['uid'] . ' sh -c "YES | cp -rf ' . $path . '/. .; ' . $chmod . '";'
            . $sql_q); // sql запрос

        $sql->query('UPDATE `web` set `update`="' . $start_point . '" WHERE `id`="' . $web['id'] . '" LIMIT 1');

        System::outjs(['s' => 'ok'], $mcache);
    }

    public static function delete($aData = [], $mcache)
    {
        global $sql;

        include(DATA . 'web.php');

        $stack = web::stack($aData, '`id`, `uid`, `unit`, `domain`, `login`');

        if (!$sql->num($stack)) {
            System::outjs(['e' => 'Дополнительная услуга не установлена.'], $mcache);
        }

        $web = $sql->get($stack);

        include(LIB . 'ssh.php');

        $unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')], $mcache);
        }

        // Директория дополнительной услуги
        $delete = '';

        if ($web['domain'] != '') {
            $delete = 'tmux new-session -ds r_w_' . $web['uid'] . ' rm -r ' . $aWebUnit['install'][$aWebUnit['unit'][$aData['type']]][$aData['type']] . $web['domain'] . ';';
        }

        $ip = System::first(explode(':', $unit['address']));

        $aDomain = explode('.', $web['domain']);
        $zone = array_pop($aDomain);

        // Удаление поддомена
        if ($aData['type'] != 'mysql') {
            $result = json_decode(file_get_contents(System::updtext($aWebUnit['isp']['domain']['delete'], ['subdomain' => $web['domain'], 'domain' => end($aDomain) . '.' . $zone, 'ip' => $ip])), true);

            if (!isset($result['result']) || strtolower($result['result']) != 'ok') {
                System::outjs(['e' => 'Не удалось удалить поддомен, обратитесь в тех.поддержку.'], $mcache);
            }
        }

        // Удаление задания crontab
        if (isset($aWebUnit['isp']['crontab'][$aData['type']]['delete']) && isset($aData['cron'])) {
            $result = json_decode(file_get_contents(System::updtext($aWebUnit['isp']['crontab'][$aData['type']]['delete'], ['data' => $aData['cron']])), true);
            if (!isset($result['result']) || strtolower($result['result']) != 'ok') {
                System::outjs(['e' => 'Не удалось удалить задание, обратитесь в тех.поддержку.'], $mcache);
            }
        }

        $sql_q = isset($aWebSQL[$aData['type']]) ? "mysql --login-path=local -e \"DROP DATABASE IF EXISTS " . $web['login'] . "; DROP USER " . $web['login'] . "\"" : '';

        $ssh->set('rm /etc/apache2/sites-enabled/' . $web['domain'] . ';' // Удаление настроек апач
            . $delete // Удаление файлов
            . 'userdel web' . $web['uid'] . ';' // Удаление пользователя
            . 'tmux new-session -ds apache_reload_' . $web['uid'] . ' service apache2 reload;' // Перезагрузить конфигурации апач
            . $sql_q); // sql запрос

        $sql->query('DELETE FROM `web` WHERE `id`="' . $web['id'] . '" LIMIT 1');

        System::outjs(['s' => 'ok'], $mcache);
    }

    public static function connect($aData = [], $mcache)
    {
        global $cfg, $sql, $start_point;

        include(DATA . 'web.php');

        $sql->query('SELECT `id`, `uid`, `unit`, `game`, `user`, `tarif`, `address`, `port`, `status`, `name` FROM `servers` WHERE `id`="' . $aData['server'] . '" AND `user`="' . $aData['user'] . '" LIMIT 1');
        if (!$sql->num()) {
            System::outjs(['e' => 'Игровой сервер не найден.'], $mcache);
        }

        $server = $sql->get();

        // Проверка статуса игрового сервера
        if (!in_array($server['status'], ['working', 'off', 'start', 'restart', 'change'])) {
            System::outjs(['e' => 'Игровой сервер недоступен для подключения.'], $mcache);
        }

        // Проверка установки плагина
        $sql->query('SELECT `id` FROM `plugins_install` WHERE `server`="' . $server['id'] . '" AND `plugin`="' . $aWebConnect[$aData['type']][$server['game']] . '" LIMIT 1');
        if (!$sql->num()) {
            System::outjs(['i' => 'Для подключения, необходимо установить плагин.', 'pid' => $aWebConnect[$aData['type']][$server['game']]], $mcache);
        }

        $aData['server'] = array_merge($server, ['id' => $aData['server']]);

        $stack = web::stack($aData, '`config`, `unit`, `login`');

        if (!$sql->num($stack)) {
            System::outjs(['e' => 'Дополнительная услуга не установлена.'], $mcache);
        }

        $web = $sql->get($stack);

        include(LIB . 'ssh.php');

        $sql->query('SELECT `passwd`, `address` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')], $mcache);
        }

        $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Директория игр. сервера
        $dir = $tarif['install'] . $server['uid'] . '/';

        // Взять rcon_password
        $get = explode(' ', str_replace('"', '', trim($ssh->get('cat ' . $dir . $aData['cfg'] . ' | grep rcon_password'))));

        $rcon = trim(end($get));

        if (!isset($rcon[0])) {
            System::outjs(['r' => 'Необходимо установить rcon пароль (rcon_password).', 'url' => $cfg['http'] . 'servers/id/' . $server['id'] . '/section/settings/subsection/server'], $mcache);
        }

        $temp = System::temp(System::updtext(base64_decode($web['config']), $aData['orcfg']));

        $ssh->setfile($temp, $dir . $aData['file']);
        $ssh->set('chmod 0644' . ' ' . $dir . $aData['file']);

        $unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')], $mcache);
        }

        // sql запросы
        $sql_q = '';

        $ip = $server['address'];
        $port = $server['port'];
        $server_address = $server['address'] . ':' . $server['port'];

        if (isset($aWebSQL[$aData['type']]['connect'])) {
            foreach ($aWebSQL[$aData['type']]['connect'] as $query) {
                $sql_q .= "mysql --login-path=local " . $web['login'] . " -e \"" . System::updtext(
                    $query,
                    array_merge(['id' => $aData['server']['id'], 'rcon' => $rcon, 'address' => $server_address, 'ip' => $ip, 'port' => $port, 'name' => $server['name'], 'time' => $start_point], $aData['orsql'])
                ) . "\";";
            }
        }

        $ssh->set('chown server' . $server['uid'] . ':servers ' . $dir . $aData['file'] . ';' // Смена владельца файла
            . $sql_q); // sql запросы

        unlink($temp);

        System::outjs(['s' => 'ok'], $mcache);
    }

    public static function passwd($aData = [], $mcache)
    {
        global $sql;

        include(DATA . 'web.php');

        $stack = web::stack($aData);

        if (!$sql->num($stack)) {
            System::outjs(['e' => 'Дополнительная услуга не установлена.'], $mcache);
        }

        $web = $sql->get($stack);

        $passwd = System::passwd($aWebParam[$aData['type']]['passwd']);

        include(LIB . 'ssh.php');

        $unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')], $mcache);
        }

        $sql_q = '';

        if (isset($aWebSQL[$aData['type']]['passwd'])) {
            foreach ($aWebSQL[$aData['type']]['passwd'] as $query) {
                $sql_q .= "mysql --login-path=local " . $web['login'] . " -e \"" . System::updtext($query, ['passwd' => $passwd]) . "\";";
            }
        }

        $ssh->set($sql_q);

        $sql->query('UPDATE `web` set `passwd`="' . $passwd . '" WHERE `id`="' . $web['id'] . '" LIMIT 1');

        System::outjs(['s' => 'ok'], $mcache);
    }

    public static function stack($aData, $select = '`id`, `unit`, `login`')
    {
        global $sql;

        include(DATA . 'web.php');

        switch ($aWebInstall[$aData['server']['game']][$aData['type']]) {
            case 'server':
                return $sql->query('SELECT ' . $select . ' FROM `web` WHERE `type`="' . $aData['type'] . '" AND `server`="' . $aData['server']['id'] . '" LIMIT 1');

            case 'user':
                return $sql->query('SELECT ' . $select . ' FROM `web` WHERE `type`="' . $aData['type'] . '" AND `user`="' . $aData['server']['user'] . '" LIMIT 1');

            case 'unit':
                return $sql->query('SELECT ' . $select . ' FROM `web` WHERE `type`="' . $aData['type'] . '" AND `user`="' . $aData['server']['user'] . '" AND `unit`="' . $aData['server']['unit'] . '" LIMIT 1');
        }

        return null;
    }

    public static function unit($aWebUnit, $type, $id)
    {
        global $sql;

        if ($aWebUnit['unit'][$type] == 'local') {
            $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $id . '" LIMIT 1');

            return $sql->get();
        }

        return ['address' => $aWebUnit['address'], 'passwd' => $aWebUnit['passwd']];
    }
}
