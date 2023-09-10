<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$title = 'Список новостей';

if ($id)
    require(SEC . 'news/news.php');
else {
    if ((array_key_exists('page', $url) && count($url) != 1) || (!array_key_exists('page', $url) && (is_countable($url) ? count($url) : 0)))
        require(ENG . '404.php');

    require(SEC . 'news/index.php');
}
