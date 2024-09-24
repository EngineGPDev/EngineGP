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
$html->set('list', isset($html->arr['question']) ? $html->arr['question'] : '');

$html->pack('main');
