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

if (!isset($nmch)) {
    $nmch = false;
}

$text = $_POST['text'] ?? (isset($url['tag']) ? urldecode($url['tag']) : sys::outjs(['none' => '']));

$mkey = md5($text . 'wiki');

if ($mcache->get($mkey) != '' and !isset($url['tag'])) {
    sys::outjs(['s' => $mcache->get($mkey)]);
}

if (!isset($text[2]) and !isset($url['tag'])) {
    sys::outjs(['s' => 'Для выполнения поиска, необходимо больше данных', $nmch]);
}

$aWiki_q = [];
$aNswer_q = [];

// Поиск по вопросу
$wiki_q = $sql->query('SELECT `id` FROM `wiki` WHERE `name` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\') OR `tags` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\') LIMIT 5');

// Поиск по тексту (ответу)
$answer_q = $sql->query('SELECT `wiki` FROM `wiki_answer` WHERE `text` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\') LIMIT 5');

// Если нет ниодного совпадения по вводимому тексту
if (!$sql->num($wiki_q) and !$sql->num($answer_q) and !isset($url['tag'])) {
    // Поиск по словам
    if (!strpos($text, ' ')) {
        $mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

        sys::outjs(['s' => 'По вашему запросу ничего не найдено']);
    }

    // Массив слов
    $aText = explode(' ', $text);

    // Метка, которая изменится в процессе, если будет найдено хоть одно совпадение
    $sWord = false;

    foreach ($aText as $word) {
        if ($word == '' || !isset($word[2])) {
            continue;
        }

        // Поиск по вопросу
        $wiki_q = $sql->query('SELECT `id` FROM `wiki` WHERE `name` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\') OR `tags` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\') LIMIT 5');

        // Поиск по тексту (ответу)
        $answer_q = $sql->query('SELECT `wiki` FROM `wiki_answer` WHERE `text` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\') LIMIT 5');

        if ($sql->num($wiki_q)) {
            $aWiki_q[] = $wiki_q;
        }

        if ($sql->num($answer_q)) {
            $aNswer_q[] = $answer_q;
        }
    }

} else {
    $aWiki_q[] = $wiki_q;
    $aNswer_q[] = $answer_q;
}

if (!count($aWiki_q) and !count($aNswer_q) and !isset($url['tag'])) {
    $mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

    sys::outjs(['s' => 'По вашему запросу ничего не найдено']);
}

// Защита от дублирования
$aResult = [];

foreach ($aWiki_q as $index => $wiki_q) {
    // Генерация списка (совпадение по вопросу)
    while ($wiki = $sql->get($wiki_q)) {
        // Проверка дублирования
        if (in_array($wiki['id'], $aResult)) {
            continue;
        }

        $sql->query('SELECT `id`, `name`, `tags`, `date` FROM `wiki` WHERE `id`="' . $wiki['id'] . '" LIMIT 1');
        $quest = $sql->get();

        $aTags = explode(',', $quest['tags']);

        $tags = '';

        foreach ($aTags as $tag) {
            $tag = trim($tag);

            $tags .= '<a href="' . $cfg['http'] . 'wiki/section/search/tag/' . $tag . '">' . $tag . '</a>';
        }

        $html->get('list', 'sections/wiki/question');

        $html->set('id', $quest['id']);
        $html->set('name', sys::find($quest['name'], $text));
        $html->set('tags', $tags != '' ? $tags : 'Теги отсутствуют');
        $html->set('date', sys::today($quest['date']));

        $html->set('home', $cfg['http']);

        $html->pack('question');

        array_push($aResult, $wiki['id']);
    }
}

foreach ($aNswer_q as $index => $answer_q) {
    // Генерация списка (совпадение по ответу)
    while ($answer = $sql->get($answer_q)) {
        // Проверка дублирования
        if (in_array($answer['wiki'], $aResult)) {
            continue;
        }

        $sql->query('SELECT `id`, `name`, `tags`, `date` FROM `wiki` WHERE `id`="' . $answer['wiki'] . '" LIMIT 1');
        $quest = $sql->get();

        $aTags = explode(',', $quest['tags']);

        $tags = '';

        foreach ($aTags as $tag) {
            $tag = trim($tag);

            $tags .= '<a href="' . $cfg['http'] . 'wiki/section/search/tag/' . $tag . '">' . $tag . '</a>';
        }

        $html->get('list', 'sections/wiki/question');

        $html->set('id', $quest['id']);
        $html->set('name', sys::find($quest['name'], $text));
        $html->set('tags', $tags != '' ? $tags : 'Теги отсутствуют');
        $html->set('date', sys::today($quest['date']));

        $html->set('home', $cfg['http']);

        $html->pack('question');
    }
}

$html->arr['question'] ??= 'По вашему запросу ничего не найдено';

$mcache->set($mkey, $html->arr['question'], false, 15);

if (!isset($url['tag'])) {
    sys::outjs(['s' => $html->arr['question']], $nmch);
}

$html->nav('Категории вопросов', $cfg['http'] . 'wiki');
$html->nav('Поиск по тегам');

$html->get('search', 'sections/wiki');

$html->set('text', $text);
$html->set('result', $html->arr['question']);

$html->pack('main');
