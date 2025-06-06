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

$text = isset($_POST['text']) ? trim($_POST['text']) : '';

$mkey = md5($text . $id);

$cache = $mcache->get($mkey);

$nmch = null;

if (is_array($cache)) {
    if ($go) {
        AdminSystem::outjs($cache, $nmch);
    }

    AdminSystem::outjs($cache);
}

if (!isset($text[2])) {
    if ($go) {
        AdminSystem::outjs(['e' => 'Для выполнения поиска, необходимо больше данных'], $nmch);
    }

    AdminSystem::outjs(['e' => '']);
}

if ($text[0] == 'i' and $text[1] == 'd') {
    $promos = $sql->query('SELECT `id`, `cod`, `value`, `discount`, `use`, `extend`, `tarif`, `time` FROM `promo` WHERE `id`="' . AdminSystem::int($text) . '" LIMIT 1');
} else {
    $like = '`id` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`cod` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`value` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\')';

    $promos = $sql->query('SELECT `id`, `cod`, `value`, `discount`, `use`, `extend`, `tarif`, `time` FROM `promo` WHERE ' . $like . ' ORDER BY `id` ASC LIMIT 20');
}

if (!$sql->num($promos)) {
    if ($go) {
        AdminSystem::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);
    }

    AdminSystem::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$list = '';

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
    $list .= '<td class="text-center"><a href="#" onclick="return promo_delete(\'' . $tarif['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$mcache->set($mkey, ['s' => $list], false, 15);

AdminSystem::outjs(['s' => $list]);
