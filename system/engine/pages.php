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

if (!$id) {
    include(ENG . '404.php');
}

$sql->query('SELECT `name`, `file` FROM `pages` WHERE `id`="' . $id . '" LIMIT 1');

if (!$sql->num()) {
    include(ENG . '404.php');
}

$page = $sql->get();

$title = $page['name'];

$html->nav($page['name']);

$html->get('page');

$html->set('content', file_get_contents(FILES . 'pages/' . $page['file']));

$html->pack('main');
