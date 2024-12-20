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

$html->nav('Список новостей');

$sql->query('SELECT `id` FROM `news`');

$aPage = sys::page($page, $sql->num(), $cfg['news_page']);

sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'news');

$sql->query('SELECT `id`, `name`, `text`, `views`, `tags`, `date` FROM `news` ORDER BY `id` DESC LIMIT ' . $aPage['num'] . ', ' . $cfg['news_page']);
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

$html->get('all', 'sections/news');
$html->set('list', $html->arr['news'] ?? '');
$html->set('pages', $html->arr['pages'] ?? '');
$html->pack('main');
