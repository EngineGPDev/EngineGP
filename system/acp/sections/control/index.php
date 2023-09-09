<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if (isset($url['subsection']) and $url['subsection'] == 'search')
    require(SEC . 'control/search.php');

if ($id)
    require(SEC . 'control/server.php');
else {
    $list = '';

    $status = array(
        'working' => '<span class="text-green">Работает</span>',
        'reboot' => 'перезагружается',
        'error' => '<span class="text-red">Не отвечает</span>',
        'install' => 'Настраивается',
        'overdue' => 'Просрочен',
        'blocked' => 'Заблокирован'
    );

    $sql->query('SELECT `id` FROM `control` WHERE `user`!="-1"');

    $aPage = sys::page($page, $sql->num(), 20);

    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/control');

    $sql->query('SELECT `id`, `user`, `address`, `time`, `date`, `status`, `limit`, `price` FROM `control` WHERE `user`!="-1" ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($ctrl = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td class="text-center">' . $ctrl['id'] . '</td>';
        $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/control/id/' . $ctrl['id'] . '">' . $ctrl['address'] . '</a></td>';
        $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $ctrl['date']) . '</td>';
        $list .= '<td class="text-center">' . $ctrl['limit'] . ' шт.</td>';
        $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'control/id/' . $ctrl['id'] . '" target="_blank">Перейти</a></td>';
        $list .= '</tr>';

        $list .= '<tr>';
        $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/users/id/' . $ctrl['user'] . '">USER_' . $ctrl['user'] . '</a></td>';
        $list .= '<td class="text-center">' . $status[$ctrl['status']] . '</td>';
        $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $ctrl['time']) . '</td>';
        $list .= '<td class="text-center">' . $ctrl['price'] . ' ' . $cfg['currency'] . '</td>';
        $list .= '<td class="text-center"><a href="#" onclick="return control_delete(\'' . $ctrl['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/control');
    $html->set('list', $list);
    $url_search = isset($url['search']) ? $url['search'] : '';
    $html->set('url_search', $url_search);
    $html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');
    $html->pack('main');
}
