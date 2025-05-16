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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$html->nav('Категории вопросов', $cfg['http'] . 'wiki');

$quest = isset($url['question']) ? System::int($url['question']) : System::back($cfg['http'] . 'wiki');

$sql->query('SELECT `name`, `cat`, `tags` FROM `wiki` WHERE `id`="' . $quest . '" LIMIT 1');
if (!$sql->num()) {
    System::back($cfg['http'] . 'wiki');
}

$wiki = $sql->get();

$sql->query('SELECT `name` FROM `wiki_category` WHERE `id`="' . $wiki['cat'] . '" LIMIT 1');
$cat = $sql->get();

$sql->query('SELECT `text` FROM `wiki_answer` WHERE `wiki`="' . $quest . '" LIMIT 1');
$answer = $sql->get();

$title = $wiki['name'];
$description = $answer['text'];
$keywords = $wiki['tags'];

$html->nav($cat['name'], $cfg['http'] . 'wiki/section/question/category/' . $wiki['cat']);
$html->nav('Ответ на вопрос');

$aTags = explode(',', $wiki['tags']);

$tags = '';

foreach ($aTags as $tag) {
    $tag = trim($tag);
    $tags .= '<a href="' . $cfg['http'] . 'wiki/section/search/tag/' . $tag . '">' . $tag . '</a>';
}

$html->get('answer', 'sections/wiki');
$html->set('id', $quest);
$html->set('question', $wiki['name']);
$html->set('text', htmlspecialchars_decode($answer['text']));
$html->set('tags', $tags != '' ? $tags : 'Теги отсутствуют');
$html->pack('main');
