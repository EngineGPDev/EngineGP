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

if ($id) {
    include(SEC . 'units/unit.php');
} else {
    $list = '';

    $sql->query('SELECT `id`, `name`, `address`, `show`, `domain` FROM `units` ORDER BY `id` ASC');
    while ($unit = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td>' . $unit['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/units/id/' . $unit['id'] . '">' . $unit['name'] . '</a></td>';
        $list .= '<td>' . $unit['address'] . '</td>';
        $list .= '<td>' . ($unit['show'] == '1' ? 'Доступна' : 'Недоступна') . '</td>';
        $list .= '<td>' . $unit['domain'] . '</td>';
        $list .= '<td><a href="#" onclick="return units_delete(\'' . $unit['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/units');

    $html->set('list', $list);

    $html->pack('main');
}
