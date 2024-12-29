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

$cats = $sql->query('SELECT `id`, `name`, `sort` FROM `wiki_category` ORDER BY `id` ASC');

$list = null;

while ($cat = $sql->get($cats)) {
    $sql->query('SELECT `name` FROM `wiki` WHERE `cat`="' . $cat['id'] . '"');
    $wiki = $sql->num();

    $list .= '<tr>';
    $list .= '<td>' . $cat['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/wiki/section/cat/id/' . $cat['id'] . '">' . $cat['name'] . '</a></td>';
    $list .= '<td class="text-center">' . $wiki . ' шт.</td>';
    $list .= '<td class="text-center">' . $cat['sort'] . '</td>';
    $list .= '<td><a href="#" onclick="return wiki_cat_delete(\'' . $cat['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$html->get('cats', 'sections/wiki');

$html->set('list', $list);

$html->pack('main');
