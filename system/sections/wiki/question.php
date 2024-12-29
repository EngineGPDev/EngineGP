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

$html->nav('Категории вопросов', $cfg['http'] . 'wiki');
$html->nav('Часто задаваемые вопросы');

$cat = isset($url['category']) ? sys::int($url['category']) : sys::back($cfg['http'] . 'wiki');

$sql->query('SELECT `name` FROM `wiki_category` WHERE `id`="' . $cat . '" LIMIT 1');
if (!$sql->num()) {
    sys::back($cfg['http'] . 'wiki');
}

$category = $sql->get();

$sql->query('SELECT `id`, `name`, `tags`, `date` FROM `wiki` WHERE `cat`="' . $cat . '" ORDER BY `id` ASC');
while ($quest = $sql->get()) {
    $aTags = explode(',', $quest['tags']);

    $tags = '';

    foreach ($aTags as $tag) {
        $tag = trim($tag);

        $tags .= '<a href="' . $cfg['http'] . 'wiki/section/search/tag/' . $tag . '">' . $tag . '</a>';
    }

    $html->get('list', 'sections/wiki/question');

    $html->set('id', $quest['id']);
    $html->set('name', $quest['name']);
    $html->set('tags', $tags != '' ? $tags : 'Теги отсутствуют');
    $html->set('date', sys::today($quest['date']));

    $html->pack('question');
}

$html->get('question', 'sections/wiki');

$html->set('category', $category['name']);
$html->set('list', $html->arr['question'] ?? '');

$html->pack('main');
