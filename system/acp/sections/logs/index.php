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
    include(SEC . 'logs/sysearch.php');
}

$list = '';

$sql->query('SELECT `id` FROM `logs_sys`');

$aPage = sys::page($page, $sql->num(), 40);

sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/logs');

$sql->query('SELECT `id`, `user`, `server`, `text`, `time` FROM `logs_sys` ORDER BY `id` DESC LIMIT ' . $aPage['num'] . ', 40');
while ($log = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $log['id'] . '</td>';
    $list .= '<td>' . $log['text'] . '</td>';

    if (!$log['user']) {
        $list .= '<td class="text-center">Система</td>';
    } else {
        $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/users/id/' . $log['user'] . '">USER_' . $log['user'] . '</a></td>';
    }

    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/servers/id/' . $log['server'] . '">SERVER_' . $log['server'] . '</a></td>';
    $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $log['time']) . '</td>';
    $list .= '</tr>';
}

$html->get('index', 'sections/logs');

$html->set('list', $list);

$html->set('pages', $html->arr['pages'] ?? '');

$html->pack('main');
