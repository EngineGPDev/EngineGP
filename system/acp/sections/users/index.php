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

if (isset($url['subsection']) and $url['subsection'] == 'search') {
    include(SEC . 'users/search.php');
}

if ($id) {
    include(SEC . 'users/user.php');
} else {
    $sort_page = '';
    $sort_sql = 'ORDER BY `id` ASC';

    if (isset($url['sort']) and in_array($url['sort'], ['id', 'balance', 'group'])) {
        $sort = 'asc';

        if (isset($url['sorting'])) {
            $sort = $url['sorting'] == 'asc' ? 'asc' : 'desc';
        }

        $sort_page = '/sort/' . $url['sort'] . '/sorting/' . $sort;
        $sort_sql = 'ORDER BY `' . $url['sort'] . '` ' . $sort;

        $sort_icon = [$url['sort'] => $sort];
    }

    $list = '';

    $aGroup = ['user' => 'Пользователь', 'support' => 'Тех. поддержка', 'admin' => 'Администратор'];

    $sql->query('SELECT `id` FROM `users`');

    $aPage = sys::page($page, $sql->num(), 20);

    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/users' . $sort_page);

    $sql->query('SELECT `id`, `login`, `mail`, `balance`, `group` FROM `users` ' . $sort_sql . ' LIMIT ' . $aPage['num'] . ', 20');
    while ($us = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td>' . $us['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/users/id/' . $us['id'] . '">' . $us['login'] . '</a></td>';
        $list .= '<td>' . $us['mail'] . '</td>';
        $list .= '<td>' . $us['balance'] . ' ' . $cfg['currency'] . '</td>';
        $list .= '<td>' . $aGroup[$us['group']] . '</td>';
        $list .= '<td><a href="#" onclick="return users_delete(\'' . $us['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/users');

    $html->set('sort_id', 'asc');
    $html->set('sort_balance', 'asc');
    $html->set('sort_group', 'asc');

    if (isset($sort_icon)) {
        $html->set('sort_' . key($sort_icon), array_shift($sort_icon));
    }

    $html->set('list', $list);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
