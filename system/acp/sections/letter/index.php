<?php

/*
 * Copyright 2018-2025 Solovev Sergei
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

use EngineGP\AdminSystem;

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
    $list .= '<td class="text-right">' . AdminSystem::today($us['time']) . '</td>';
    $list .= '</tr>';
}

$html->get('index', 'sections/letter');

$html->set('list', $list);

$html->pack('main');
