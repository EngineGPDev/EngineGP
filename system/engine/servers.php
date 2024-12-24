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

// Проверка на авторизацию
sys::noauth();

if (!$id and $user['group'] == 'user') {
    $servers = $sql->query('SELECT `id` FROM `servers` WHERE `user`="' . $user['id'] . '" LIMIT 1');
    $owners = $sql->query('SELECT `id` FROM `owners` WHERE `user`="' . $user['id'] . '" LIMIT 1');

    if (!$sql->num($servers) and !$sql->num($owners)) {
        sys::back($cfg['http'] . 'services');
    } // Если нет игровых серверов отправить на страницу аренды
}

if ($id and !$section) {
    $section = 'index';
}

$title = 'Управление игровыми серверами';

// Подключение раздела
if (in_array($section, ['action', 'scan', 'index', 'console', 'settings', 'plugins', 'maps', 'owners', 'filetp', 'tarif', 'copy', 'graph', 'web', 'boost', 'rcon'])) {
    if (!$id) {
        sys::back($cfg['http'] . 'servers');
    }

    if ($user['group'] == 'admin' || ($user['group'] == 'support' and $user['level'])) {
        if ($user['group'] == 'support' and $user['level'] < 2) {
            $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" AND `user`="' . $user['id'] . '" LIMIT 1');
            if (!$sql->num()) {
                $sql->query('SELECT `id` FROM `help` WHERE `type`="server" AND `service`="' . $id . '" LIMIT 1');
                if (!$sql->num()) {
                    $sql->query('SELECT `rights` FROM `owners` WHERE `server`="' . $id . '" AND `user`="' . $user['id'] . '" LIMIT 1');
                    if (!$sql->num()) {
                        sys::back($cfg['http'] . 'servers');
                    }

                    $owner = $sql->get();

                    $rights = sys::b64djs($owner['rights']);

                    if ($section == 'action') {
                        if (!isset($rights[$url['action']]) || !$rights[$url['action']]) {
                            sys::outjs(['e' => 'У вас нет доступа к данному серверу']);
                        }
                    } else {
                        if (!in_array($section, ['index', 'scan']) and (!isset($rights[$section]) || !$rights[$section])) {
                            sys::back($cfg['http'] . 'servers');
                        }
                    }
                }
            }
        }

        $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
    } else {
        $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" AND `user`="' . $user['id'] . '" LIMIT 1');
        if (!$sql->num()) {
            $sql->query('SELECT `rights` FROM `owners` WHERE `server`="' . $id . '" AND `user`="' . $user['id'] . '" LIMIT 1');
            if (!$sql->num()) {
                sys::back($cfg['http'] . 'servers');
            }

            $owner = $sql->get();

            $rights = sys::b64djs($owner['rights']);

            if ($section == 'action') {
                if (!isset($rights[$url['action']]) || !$rights[$url['action']]) {
                    sys::outjs(['e' => sys::text('error', 'ser_owner')]);
                }
            } else {
                if (!in_array($section, ['index', 'scan']) and (!isset($rights[$section]) || !$rights[$section])) {
                    sys::back($cfg['http'] . 'servers/id/' . $owner['server']);
                }
            }

            $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
        }
    }

    if (!$sql->num()) {
        sys::back($cfg['http'] . 'servers');
    } // Если нет игрового сервера отправить на страницу списка

    $html->nav($title, $cfg['http'] . 'servers');

    $file_section = file_exists(SEC . 'servers/' . $section . '.php');
    if ($file_section) {
        include(SEC . 'servers/' . $section . '.php');
    } else {
        sys::back($cfg['http'] . 'servers/id/' . $id);
    }

} else {
    $html->nav($title);

    if ($user['group'] == 'user' and $mcache->get('servers_' . $user['id']) != '') {
        $html->arr['main'] = $mcache->get('servers_' . $user['id']);
    } else {
        include(SEC . 'servers/list.php');
        include(SEC . 'servers/owners_list.php');

        $html->get('servers', 'sections/servers');
        $html->set('list', $html->arr['list'] ?? 'У вас нет игровых серверов', true);
        $html->set('wait_servers', $wait_servers);
        $html->set('updates_servers', $updates_servers);
        $html->pack('main');

        $mcache->set('servers_' . $user['id'], $html->arr['main'], false, 4);
    }
}
