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

if ($id) {
    include(SEC . 'pages/page.php');
} else {
    $list = '';

    $sql->query('SELECT `id` FROM `pages`');

    $aPage = sys::page($page, $sql->num(), 20);

    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/pages');

    $sql->query('SELECT * FROM `pages` ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($page = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td>' . $page['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/pages/id/' . $page['id'] . '">' . $page['name'] . '</a></td>';
        $list .= '<td>' . $page['file'] . '</td>';
        $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'pages/id/' . $page['id'] . '" target="_blank">Перейти</a></td>';
        $list .= '<td class="text-center"><a href="#" onclick="return page_delete(\'' . $page['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/pages');

    $html->set('list', $list);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
