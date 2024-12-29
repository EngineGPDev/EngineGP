<?php

/*
 * Copyright 2018-2024 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$list = '';

$uss = $sql->query('SELECT `id`, `login`, `mail`, `balance` FROM `users` WHERE `group`="user" AND `balance`!="0" ORDER BY `balance` DESC LIMIT 10');
while ($us = $sql->get($uss)) {
    $sql->query('SELECT `id` FROM `servers` WHERE `user`="' . $us['id'] . '" LIMIT 10');
    $servers = $sql->num();

    $list .= '<tr>';
    $list .= '<td>' . $us['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['url'] . '/acp/users/id/' . $us['id'] . '">' . $us['login'] . '</a></td>';
    $list .= '<td>' . $us['mail'] . '</td>';
    $list .= '<td>' . $us['balance'] . ' ' . $cfg['currency'] . '</td>';
    $list .= '<td class="text-right">' . $servers . ' шт.</td>';
    $list .= '</tr>';
}

$html->get('stats', 'sections/users');

$html->set('list', $list);

$sql->query('SELECT `balance` FROM `users` WHERE `group`="user" AND `balance`!="0"');
$html->set('users', $sql->num());

$money = 0;
while ($us = $sql->get()) {
    $money += $us['balance'];
}

$html->set('money', $money);

$html->pack('main');
