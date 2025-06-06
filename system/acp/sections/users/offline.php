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

$list = '';

$aGroup = ['user' => 'Пользователь', 'support' => 'Тех. поддержка', 'admin' => 'Администратор'];

$sql->query('SELECT `id` FROM `users` WHERE `time`<"' . ($start_point - 181) . '"');

$aPage = AdminSystem::page($page, $sql->num(), 20);

AdminSystem::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/users/section');

$sql->query('SELECT `id`, `login`, `mail`, `group`, `time` FROM `users` WHERE `time`<"' . ($start_point - 181) . '" ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
while ($us = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $us['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/users/id/' . $us['id'] . '">' . $us['login'] . '</a></td>';
    $list .= '<td>' . $us['mail'] . '</td>';
    $list .= '<td>' . $aGroup[$us['group']] . '</td>';
    $list .= '<td>' . AdminSystem::today($us['time']) . '</td>';
    $list .= '<td><a href="#" onclick="return users_delete(\'' . $us['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$html->get('offline', 'sections/users');

$html->set('list', $list);
$html->set('pages', $html->arr['pages'] ?? '');

$html->pack('main');
