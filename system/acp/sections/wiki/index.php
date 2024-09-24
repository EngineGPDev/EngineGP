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

if (isset($url['subsection']) and $url['subsection'] == 'search') {
    include(SEC . 'wiki/search.php');
}

if ($id) {
    include(SEC . 'wiki/wiki.php');
} else {
    $list = '';

    $sql->query('SELECT `id` FROM `wiki`');

    $aPage = sys::page($page, $sql->num(), 20);

    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/wiki');

    $wikis = $sql->query('SELECT `id`, `name`, `cat`, `date` FROM `wiki` ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($wiki = $sql->get($wikis)) {
        $sql->query('SELECT `name` FROM `wiki_category` WHERE `id`="' . $wiki['cat'] . '" LIMIT 1');
        $cat = $sql->get();

        $list .= '<tr>';
        $list .= '<td>' . $wiki['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/wiki/id/' . $wiki['id'] . '">' . $wiki['name'] . '</a></td>';
        $list .= '<td>' . $cat['name'] . '</td>';
        $list .= '<td>' . date('d.m.Y - H:i:s', $wiki['date']) . '</td>';
        $list .= '<td class="text-center"><a href="#" onclick="return wiki_delete(\'' . $wiki['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/wiki');

    $html->set('list', $list);

    $html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

    $html->pack('main');
}
