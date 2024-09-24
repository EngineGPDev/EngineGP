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

$title = 'Список новостей';

if ($id) {
    include(SEC . 'news/news.php');
} else {
    if ((array_key_exists('page', $url) && count($url) != 1) || (!array_key_exists('page', $url) && count($url))) {
        include(ENG . '404.php');
    }

    include(SEC . 'news/index.php');
}
