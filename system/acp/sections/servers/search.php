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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$text = isset($_POST['text']) ? trim($_POST['text']) : '';

$mkey = md5($text . $id);

$cache = $mcache->get($mkey);

$nmch = null;

if (is_array($cache)) {
    if ($go) {
        AdminSystem::outjs($cache, $nmch);
    }

    AdminSystem::outjs($cache);
}

if (!isset($text[2])) {
    if ($go) {
        AdminSystem::outjs(['e' => 'Для выполнения поиска, необходимо больше данных'], $nmch);
    }

    AdminSystem::outjs(['e' => '']);
}

$select = '`id`, `unit`, `tarif`, `user`, `address`, `port`, `game`, `status`, `slots`, `name`, `time` FROM `servers` WHERE `user`!="-1" AND';

if (isset($url['search']) and in_array($url['search'], ['unit', 'tarif'])) {
    $select .= ' `' . $url['search'] . '`=' . AdminSystem::int($url[$url['search']]) . ' AND';
}

$check = explode('=', $text);

if (in_array($check[0], ['game', 'unit', 'tarif', 'user', 'status', 'slots'])) {
    $val = trim($check[1]);

    switch ($check[0]) {
        case 'game':
            if (in_array($val, ['cs', 'cssold', 'css', 'csgo',' cs2', 'samp', 'crmp', 'mta', 'mc'])) {
                $servers = $sql->query('SELECT ' . $select . ' FROM `servers` WHERE `user`!="-1" AND `game`="' . $val . '" ORDER BY `id` ASC');
            }
            break;

        case 'unit':
            $servers = $sql->query('SELECT ' . $select . ' `unit`="' . AdminSystem::int($val) . '" ORDER BY `id` ASC');
            break;

        case 'tarif':
            $servers = $sql->query('SELECT ' . $select . ' `tarif`="' . AdminSystem::int($val) . '" ORDER BY `id` ASC');
            break;

        case 'user':
            $servers = $sql->query('SELECT ' . $select . ' `user`="' . AdminSystem::int($val) . '" ORDER BY `id` ASC');
            break;

        case 'status':
            if (in_array($val, ['working', 'start', 'change', 'restart', 'off', 'overdue', 'blocked', 'recovery', 'reinstall', 'update', 'install'])) {
                $servers = $sql->query('SELECT ' . $select . ' `status`="' . $val . '" ORDER BY `id` ASC');
            }
            break;

        case 'slots':
            $servers = $sql->query('SELECT ' . $select . ' `slots`="' . AdminSystem::int($val) . '" ORDER BY `id` ASC');
            break;
    }
} elseif ($text[0] == 'i' and $text[1] == 'd') {
    $servers = $sql->query('SELECT ' . $select . ' `id`="' . AdminSystem::int($text) . '" LIMIT 1');
} else {
    $like = '`id` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`name` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`game` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`slots` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`status` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`address` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`port` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\')';

    $servers = $sql->query('SELECT ' . $select . ' (' . $like . ') ORDER BY `id` ASC');
}

if (!$sql->num($servers)) {
    if ($go) {
        AdminSystem::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);
    }

    AdminSystem::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$status = [
    'working' => '<span class="text-green">Работает</span>',
    'off' => '<span class="text-red">Выключен</span>',
    'start' => 'Запускается',
    'restart' => 'Перезапускается',
    'change' => 'Смена карты',
    'install' => 'Устанавливается',
    'reinstall' => 'Переустанавливается',
    'update' => 'Обновляется',
    'recovery' => 'Восстанавливается',
    'overdue' => 'Просрочен',
    'blocked' => 'Заблокирован',
];

$list = '';

while ($server = $sql->get($servers)) {
    $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $sql->query('SELECT `name` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    $server_address = $server['address'] . ':' . $server['port'];

    $list .= '<tr>';
    $list .= '<td class="text-center">' . $server['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/servers/id/' . $server['id'] . '">' . $server['name'] . '</a></td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/servers/search/unit/unit/' . $server['unit'] . '">#' . $server['unit'] . ' ' . $unit['name'] . '</a></td>';
    $list .= '<td class="text-center">' . $server['slots'] . ' шт.</td>';
    $list .= '<td class="text-center">' . strtoupper($server['game']) . '</td>';
    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'servers/id/' . $server['id'] . '" target="_blank">Перейти</a></td>';
    $list .= '</tr>';

    $list .= '<tr>';
    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/users/id/' . $server['user'] . '">USER_' . $server['user'] . '</a></td>';
    $list .= '<td>' . $server_address . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/servers/search/tarif/tarif/' . $server['tarif'] . '">#' . $server['tarif'] . ' ' . $tarif['name'] . '</a></td>';
    $list .= '<td class="text-center">' . $status[$server['status']] . '</td>';
    $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $server['time']) . '</td>';
    $list .= '<td class="text-center"><a href="#" onclick="return servers_delete(\'' . $server['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$mcache->set($mkey, ['s' => $list], false, 15);

AdminSystem::outjs(['s' => $list]);
