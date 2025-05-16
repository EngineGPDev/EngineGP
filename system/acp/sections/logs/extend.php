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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$list = '';

$sql->query('SELECT `id` FROM `logs` WHERE `type`="extend"');

$aPage = sys::page($page, $sql->num(), 40);

sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/logs/section/extend');

$sql->query('SELECT `id`, `user`, `text`, `date`, `money` FROM `logs` WHERE `type`="extend" ORDER BY `id` DESC LIMIT ' . $aPage['num'] . ', 40');
while ($log = $sql->get()) {
    $list .= '<tr>';
    $list .= '<td>' . $log['id'] . '</td>';
    $list .= '<td>' . $log['text'] . '</td>';
    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/users/id/' . $log['user'] . '">USER_' . $log['user'] . '</a></td>';
    $list .= '<td class="text-center">' . $log['money'] . '</td>';
    $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $log['date']) . '</td>';
    $list .= '</tr>';
}

$html->get('logs', 'sections/logs');

$html->set('list', $list);

$html->set('pages', $html->arr['pages'] ?? '');

$html->pack('main');
