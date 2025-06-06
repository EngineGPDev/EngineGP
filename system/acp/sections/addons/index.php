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

if (isset($url['subsection']) and $url['subsection'] == 'search') {
    include(SEC . 'addons/search.php');
}

if ($id) {
    include(SEC . 'addons/plugin.php');
} else {
    $sort_page = '';
    $sort_sql = 'ORDER BY `id` ASC';

    if (isset($url['sort']) and in_array($url['sort'], ['id', 'cat', 'game'])) {
        $sort = 'asc';

        if (isset($url['sorting'])) {
            $sort = $url['sorting'] == 'asc' ? 'asc' : 'desc';
        }

        $sort_page = '/sort/' . $url['sort'] . '/sorting/' . $sort;
        $sort_sql = 'ORDER BY `' . $url['sort'] . '` ' . $sort;

        $sort_icon = [$url['sort'] => $sort];
    }

    $list = '';

    $sql->query('SELECT `id` FROM `plugins`');

    $aPage = AdminSystem::page($page, $sql->num(), 20);

    AdminSystem::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/addons' . $sort_page);

    $status = [0 => 'Стабильный', 1 => 'Нестабильный', 2 => 'Тестируемый'];

    $plugins = $sql->query('SELECT `id`, `cat`, `game`, `name`, `status` FROM `plugins` ' . $sort_sql . ' LIMIT ' . $aPage['num'] . ', 20');
    while ($plugin = $sql->get($plugins)) {
        $sql->query('SELECT `name` FROM `plugins_category` WHERE `id`="' . $plugin['cat'] . '" LIMIT 1');
        $cat = $sql->get();

        $list .= '<tr>';
        $list .= '<td>' . $plugin['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/addons/id/' . $plugin['id'] . '">' . $plugin['name'] . '</a></td>';
        $list .= '<td>' . $cat['name'] . '</td>';
        $list .= '<td>' . $status[$plugin['status']] . '</td>';
        $list .= '<td>' . strtoupper($plugin['game']) . '</td>';
        $list .= '<td><a href="#" onclick="return plugins_delete(\'' . $plugin['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/addons');

    $html->set('sort_id', 'asc');
    $html->set('sort_cat', 'asc');
    $html->set('sort_game', 'asc');

    if (isset($sort_icon)) {
        $html->set('sort_' . key($sort_icon), array_shift($sort_icon));
    }

    $html->set('list', $list);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
