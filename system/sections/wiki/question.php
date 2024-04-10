<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$html->nav('Категории вопросов', $cfg['http'] . 'wiki');
$html->nav('Часто задаваемые вопросы');

$cat = isset($url['category']) ? sys::int($url['category']) : sys::back($cfg['http'] . 'wiki');

$sql->query('SELECT `name` FROM `wiki_category` WHERE `id`="' . $cat . '" LIMIT 1');
if (!$sql->num())
    sys::back($cfg['http'] . 'wiki');

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
