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

$html->nav('Список новостей', $cfg['http'] . 'news');

$sql->query('SELECT `id`, `name`, `full_text`, `views`, `tags`, `date` FROM `news` WHERE `id`="' . $id . '" LIMIT 1');

if (!$sql->num())
    include(ENG . '404.php');

$news = $sql->get();

$sql->query('UPDATE `news` set `views`="' . ($news['views'] + 1) . '" WHERE `id`="' . $id . '" LIMIT 1');

$text = htmlspecialchars_decode($news['full_text']);

$title = $news['name'];
$description = $text;
$keywords = $news['tags'];

$html->nav($news['name']);

$html->get('news', 'sections/news');

$html->set('id', $news['id']);
$html->set('name', htmlspecialchars_decode($news['name']));
$html->set('text', $text);
$html->set('views', $news['views']);
$html->set('tags', sys::tags($news['tags']));
$html->set('date', sys::today($news['date']));

$html->pack('main');
