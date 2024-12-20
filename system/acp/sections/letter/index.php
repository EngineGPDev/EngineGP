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

$group = ['user' => 'Пользователь', 'support' => 'Тех.поддержка', 'admin' => 'Администратор'];
$list = '';

$sql->query('SELECT `id`, `login`, `mail`, `group`, `name`, `lastname`, `patronymic`, `balance`, `time` FROM `users` WHERE `notice_news`="1" ORDER BY `id` ASC');
while ($us = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td class="text-center"><input id="letter_' . $us['id'] . '" class="letter" type="checkbox" name="users[' . $us['id'] . ']"></td>';
    $list .= '<td><label for="letter_' . $us['id'] . '">' . $us['login'] . ' / ' . $us['lastname'] . ' ' . $us['name'] . ' ' . $us['patronymic'] . '</label></td>';
    $list .= '<td>' . $us['mail'] . '</td>';
    $list .= '<td class="text-center">' . $us['balance'] . ' ' . $cfg['currency'] . '</td>';
    $list .= '<td class="text-right">' . sys::today($us['time']) . '</td>';
    $list .= '</tr>';
}

$html->get('index', 'sections/letter');

$html->set('list', $list);

$html->pack('main');
