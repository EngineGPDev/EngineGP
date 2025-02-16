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

if (isset($url['delete'])) {
    $sql->query('DELETE FROM `signup` WHERE `id`="' . $id . '" LIMIT 1');

    AdminSystem::outjs(['s' => 'ok']);
}

$list = '';

$sql->query('SELECT `id`, `mail`, `key`, `date` FROM `signup` ORDER BY `id` ASC');
while ($sign = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $sign['id'] . '</td>';
    $list .= '<td>' . $sign['mail'] . '</td>';
    $list .= '<td>' . $sign['key'] . '</td>';
    $list .= '<td>' . AdminSystem::today($sign['date']) . '</td>';
    $list .= '<td><a href="#" onclick="return users_delete_signup(\'' . $sign['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$html->get('signup', 'sections/users');

$html->set('list', $list);

$html->pack('main');
