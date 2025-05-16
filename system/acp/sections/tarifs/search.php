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

if (substr($text, 0, 5) == 'game=') {
    $game = trim(substr($text, 5));

    if (in_array($game, ['cs', 'cssold', 'css', 'csgo', 'cs2', 'samp', 'crmp', 'mta', 'mc'])) {
        $tarifs = $sql->query('SELECT `id`, `unit`, `game`, `name`, `slots_min`, `slots_max`, `port_min`, `port_max` FROM `tarifs` WHERE `game`="' . $game . '" ORDER BY `id` ASC');
    }
} elseif ($text[0] == 'i' and $text[1] == 'd') {
    $tarifs = $sql->query('SELECT `id`, `unit`, `game`, `name`, `slots_min`, `slots_max`, `port_min`, `port_max` FROM `tarifs` WHERE `id`="' . AdminSystem::int($text) . '" LIMIT 1');
} else {
    $like = '`id` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`name` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`game` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`slots_min` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`slots_max` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`port_min` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') OR'
        . '`port_max` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\')';

    $tarifs = $sql->query('SELECT `id`, `unit`, `game`, `name`, `slots_min`, `slots_max`, `port_min`, `port_max` FROM `tarifs` WHERE ' . $like . ' ORDER BY `id` ASC LIMIT 10');
}

if (!$sql->num($tarifs)) {
    if ($go) {
        AdminSystem::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);
    }

    AdminSystem::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$list = '';

while ($tarif = $sql->get($tarifs)) {
    $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $tarif['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $list .= '<tr>';
    $list .= '<td>' . $tarif['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/tarif/id/' . $tarif['id'] . '">' . $tarif['name'] . '</a></td>';
    $list .= '<td>' . $unit['name'] . '</td>';
    $list .= '<td>' . $tarif['slots_min'] . '-' . $tarif['slots_max'] . '</td>';
    $list .= '<td>' . $tarif['port_min'] . '-' . $tarif['port_max'] . '</td>';
    $list .= '<td>' . strtoupper($tarif['game']) . '</td>';
    $list .= '<td><a href="#" onclick="return tarifs_delete(\'' . $tarif['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$mcache->set($mkey, ['s' => $list], false, 15);

AdminSystem::outjs(['s' => $list]);
