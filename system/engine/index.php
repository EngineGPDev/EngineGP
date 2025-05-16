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

$title = 'Главная страница';
$html->nav($title);

$sql->query('SELECT `id`, `name`, `text`, `views`, `tags`, `date` FROM `news` ORDER BY `id` DESC LIMIT 3');
while ($news = $sql->get()) {
    $html->get('list', 'sections/news');

    $html->set('id', $news['id']);
    $html->set('name', htmlspecialchars_decode($news['name']));
    $html->set('text', htmlspecialchars_decode($news['text']));
    $html->set('views', $news['views']);
    $html->set('tags', sys::tags($news['tags']));
    $html->set('date', sys::today($news['date']));

    $html->pack('news');
}

$html->get('index');

$html->set('news', $html->arr['news'] ?? '');
$html->pack('main');
