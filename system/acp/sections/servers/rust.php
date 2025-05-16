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

if ($id) {
    include(SEC . 'servers/server.php');
} else {
    $list = '';

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

    $select = 'WHERE `user`!="-1"';
    $url_search = '';

    if (isset($url['search']) and in_array($url['search'], ['unit', 'tarif'])) {
        $select = 'WHERE `' . $url['search'] . '`="' . sys::int($url[$url['search']]) . '" AND `user`!="-1"';
        $url_search = '/search/' . $url['search'] . '/' . $url['search'] . '/' . $url[$url['search']];
    }

    $sql->query('SELECT `id` FROM `servers` ' . $select . ' AND `game`="rust"');

    $aPage = sys::page($page, $sql->num(), 20);

    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/servers/section/rust' . $url_search);

    $servers = $sql->query('SELECT `id`, `unit`, `tarif`, `user`, `address`, `game`, `status`, `slots`, `name`, `time` FROM `servers` ' . $select . ' AND `game`="rust" ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($server = $sql->get($servers)) {
        $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $sql->query('SELECT `name` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

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
        $list .= '<td>' . $server['address'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/servers/search/tarif/tarif/' . $server['tarif'] . '">#' . $server['tarif'] . ' ' . $tarif['name'] . '</a></td>';
        $list .= '<td class="text-center">' . $status[$server['status']] . '</td>';
        $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $server['time']) . '</td>';
        $list .= '<td class="text-center"><a href="#" onclick="return servers_delete(\'' . $server['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/servers');

    $html->set('list', $list);

    $html->set('url_search', $url_search);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
