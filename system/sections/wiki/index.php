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

$html->nav('Категории вопросов');

$cats = $sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `sort` ASC');
while ($cat = $sql->get($cats)) {
    $sql->query('SELECT `id` FROM `wiki` WHERE `cat`="' . $cat['id'] . '" LIMIT 1');
    if (!$sql->num()) {
        continue;
    }

    $html->get('list', 'sections/wiki/category');

    $html->set('id', $cat['id']);
    $html->set('name', $cat['name']);

    $html->pack('category');
}

$html->get('category', 'sections/wiki');

$html->set('list', $html->arr['category'] ?? '');

$html->pack('main');
