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
    include(SEC . 'news/search.php');
}

if ($id) {
    include(SEC . 'news/news.php');
} else {
    $list = '';

    $sql->query('SELECT `id` FROM `news`');

    $aPage = sys::page($page, $sql->num(), 20);

    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/news');

    $sql->query('SELECT `id`, `name`, `tags`, `views`, `date` FROM `news` ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($news = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td>' . $news['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/news/id/' . $news['id'] . '">' . $news['name'] . '</a></td>';
        $list .= '<td>' . $news['tags'] . '</td>';
        $list .= '<td class="text-center">' . $news['views'] . '</td>';
        $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $news['date']) . '</td>';
        $list .= '<td class="text-center"><a href="#" onclick="return news_delete(\'' . $news['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/news');

    $html->set('list', $list);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
