<?php

/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$html->nav('Категории вопросов', $cfg['http'] . 'wiki');

$quest = isset($url['question']) ? sys::int($url['question']) : sys::back($cfg['http'] . 'wiki');

$sql->query('SELECT `name`, `cat`, `tags` FROM `wiki` WHERE `id`="' . $quest . '" LIMIT 1');
if (!$sql->num()) {
    sys::back($cfg['http'] . 'wiki');
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
