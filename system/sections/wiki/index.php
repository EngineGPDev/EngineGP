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

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$html->nav('Категории вопросов');

$cats = $sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `sort` ASC');
while ($cat = $sql->get($cats)) {
    $sql->query('SELECT `id` FROM `wiki` WHERE `cat`="' . $cat['id'] . '" LIMIT 1');
    if (!$sql->num())
        continue;

    $html->get('list', 'sections/wiki/category');

    $html->set('id', $cat['id']);
    $html->set('name', $cat['name']);

    $html->pack('category');
}

$html->get('category', 'sections/wiki');

$html->set('list', isset($html->arr['category']) ? $html->arr['category'] : '');

$html->pack('main');
