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

$text = isset($_POST['text']) ? str_ireplace('.vpk', '', $_POST['text']) : '';

$mkey = md5($text . $id);

$cache = $mcache->get($mkey);

if (is_array($cache)) {
    if ($go) {
        sys::outjs($cache, $nmch);
    }

    sys::outjs($cache);
}

if (!isset($text[2])) {
    if ($go) {
        sys::outjs(['e' => 'Для выполнения поиска, необходимо больше данных'], $nmch);
    }

    sys::outjs(['e' => '']);
}

// Поиск по картам
if ($text[0] == '^') {
    $sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" AND `name` REGEXP FROM_BASE64(\'' . base64_encode(str_replace('_', '\_', $text) . '') . '\') ORDER BY `name` ASC LIMIT 12');
    $text = substr($text, 1);
} else {
    $sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" AND `name` LIKE FROM_BASE64(\'' . base64_encode('%' . str_replace('_', '\_', $text) . '%') . '\') ORDER BY `name` ASC LIMIT 12');
}

if (!$sql->num()) {
    if ($go) {
        sys::outjs(['e' => 'По вашему запросу ничего не найдено'], $nmch);
    }

    sys::outjs(['e' => 'По вашему запросу ничего не найдено']);
}

$i = 0;
$mapsjs = '';

while ($map = $sql->get()) {
    $i += 1;

    $mapsjs[$i] = 's' . $map['id'];

    $html->get('map_search', 'sections/servers/games/maps');

    $html->set('id', 's' . $map['id']);
    $html->set('img', sys::img($map['name'], $server['game']));
    $html->set('name', sys::find($map['name'], $text));

    $html->pack('maps');
}

$mcache->set($mkey, ['maps' => $html->arr['maps'], 'mapsjs' => $mapsjs], false, 15);

sys::outjs(['maps' => $html->arr['maps'], 'mapsjs' => $mapsjs]);
