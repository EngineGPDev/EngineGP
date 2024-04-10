<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$cats = $sql->query('SELECT `id`, `game`, `name`, `sort` FROM `plugins_category` ORDER BY `game` ASC');
while ($cat = $sql->get($cats)) {
    $sql->query('SELECT `name` FROM `plugins` WHERE `cat`="' . $cat['id'] . '"');
    $plugins = $sql->num();

    $list .= '<tr>';
    $list .= '<td>' . $cat['id'] . '</td>';
    $list .= '<td>' . $cat['name'] . '</td>';
    $list .= '<td class="text-center">' . strtoupper($cat['game']) . '</td>';
    $list .= '<td class="text-center">' . $plugins . ' шт.</td>';
    $list .= '<td class="text-center">' . $cat['sort'] . '</td>';
    $list .= '<td><a href="#" onclick="return cats_delete(\'' . $cat['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$html->get('cats', 'sections/addons');

$html->set('list', $list);

$html->pack('main');
