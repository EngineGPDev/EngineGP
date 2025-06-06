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

if (isset($url['subsection']) and $url['subsection'] == 'search') {
    include(SEC . 'promo/search.php');
}

if ($id) {
    include(SEC . 'promo/promo.php');
} else {
    $list = '';

    $sql->query('SELECT `id` FROM `promo`');

    $aPage = AdminSystem::page($page, $sql->num(), 20);

    AdminSystem::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/promo');

    $promos = $sql->query('SELECT `id`, `cod`, `value`, `discount`, `use`, `extend`, `tarif`, `time` FROM `promo` WHERE `time`>"' . $start_point . '" ORDER BY `id` ASC LIMIT ' . $aPage['num'] . ', 20');
    while ($promo = $sql->get($promos)) {
        $sql->query('SELECT `name` FROM `tarifs` WHERE `id`="' . $promo['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        $list .= '<tr>';
        $list .= '<td>' . $promo['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/promo/id/' . $promo['id'] . '">' . $promo['cod'] . '</a></td>';
        $list .= '<td class="text-center">' . $promo['value'] . '</td>';
        $list .= '<td class="text-center">#' . $promo['tarif'] . ' ' . $tarif['name'] . '</td>';
        $list .= '<td class="text-center">' . ($promo['discount'] ? 'Скидка' : 'Подарочные дни') . '</td>';
        $list .= '<td class="text-center">' . ($promo['extend'] ? 'Продление' : 'Аренда') . '</td>';
        $list .= '<td class="text-center">' . $promo['use'] . ' шт.</td>';
        $list .= '<td class="text-center">' . date('d.m.Y - H:i:s', $promo['time']) . '</td>';
        $list .= '<td class="text-center"><a href="#" onclick="return promo_delete(\'' . $promo['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/promo');

    $html->set('list', $list);

    $html->set('pages', $html->arr['pages'] ?? '');

    $html->pack('main');
}
