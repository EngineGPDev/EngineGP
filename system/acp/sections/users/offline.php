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

$list = '';

$aGroup = array('user' => 'Пользователь', 'support' => 'Тех. поддержка', 'admin' => 'Администратор');

$sql->query('SELECT `id` FROM `users` WHERE `time`<"' . ($start_point - 181) . '"');

$aPage = sys::page($page, $sql->num(), 20);

sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/users/section');

$sql->query('SELECT `id`, `login`, `mail`, `group`, `time` FROM `users` WHERE `time`<"' . ($start_point - 181) . '" ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
while ($us = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $us['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/users/id/' . $us['id'] . '">' . $us['login'] . '</a></td>';
    $list .= '<td>' . $us['mail'] . '</td>';
    $list .= '<td>' . $aGroup[$us['group']] . '</td>';
    $list .= '<td>' . sys::today($us['time']) . '</td>';
    $list .= '<td><a href="#" onclick="return users_delete(\'' . $us['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$html->get('offline', 'sections/users');

$html->set('list', $list);
$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

$html->pack('main');
