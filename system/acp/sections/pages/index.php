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

if ($id) {
    include(SEC . 'pages/page.php');
} else {
    $list = '';

    $sql->query('SELECT `id` FROM `pages`');

    $aPage = AdminSystem::page($page, $sql->num(), 20);

    AdminSystem::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/pages');

    $sql->query('SELECT * FROM `pages` ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($page = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td>' . $page['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/pages/id/' . $page['id'] . '">' . $page['name'] . '</a></td>';
        $list .= '<td>' . $page['file'] . '</td>';
        $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'pages/id/' . $page['id'] . '" target="_blank">Перейти</a></td>';
        $list .= '<td class="text-center"><a href="#" onclick="return page_delete(\'' . $page['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/pages');

    $html->set('list', $list);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
