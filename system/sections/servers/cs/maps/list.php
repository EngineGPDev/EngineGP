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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$html->nav('Установка карт');

// Категории для быстрой сортировки
$html->get('types', 'sections/servers/' . $server['game'] . '/maps');
$html->set('id', $id);
$html->pack('types');

$type = false;

include(DATA . 'maps.php');
include(LIB . 'games/games.php');

if (isset($url['type']) and array_key_exists($url['type'], $aFindMap[$server['game']])) {
    $type = $url['type'];
}

if ($type) {
    $qsql = games::mapsql($aFindMap[$server['game']][$type]);

    $all = $mcache->get('maps_' . $server['game'] . '_' . $type);

    if (!$all) {
        $sql->query('SELECT `id` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" ' . $qsql);
        $all = $sql->num();

        $mcache->set('maps_' . $server['game'] . '_' . $type, $all, false, 120);
    }

    // Массив для построения страниц
    $aPage = System::page($page, $all, 60);

    // Генерация массива ($html->arr['pages']) страниц
    System::page_gen($aPage['ceil'], $page, $aPage['page'], 'servers/id/' . $id . '/section/maps/subsection/list/type/' . $type);

    $sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" ' . $qsql . ' ORDER BY `name` ASC LIMIT ' . $aPage['num'] . ', 30');
} else {
    $all = $mcache->get('maps_' . $server['game']);

    if (!$all) {
        $sql->query('SELECT `id` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '"');
        $all = $sql->num();

        $mcache->set('maps_' . $server['game'], $all, false, 120);
    }

    // Массив для построения страниц
    $aPage = System::page($page, $all, 30);

    // Генерация массива ($html->arr['pages']) страниц
    System::page_gen($aPage['ceil'], $page, $aPage['page'], 'servers/id/' . $id . '/section/maps/subsection/list');

    $sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" ORDER BY `name` ASC LIMIT ' . $aPage['num'] . ', 30');
}

$mapsjs = '';
$i = 0;

while ($map = $sql->get()) {
    $i += 1;

    $mapsjs .= $i . ' : "' . $map['id'] . '",';

    $html->get('map_install', 'sections/servers/games/maps');
    $html->set('id', $map['id']);
    $html->set('img', System::img($map['name'], $server['game']));
    $html->set('name', $map['name']);
    $html->pack('maps');
}

$html->get('install', 'sections/servers/games/maps');
$html->set('id', $id);
$html->set('types', $html->arr['types'] ?? '');
$html->set('maps', $html->arr['maps'] ?? 'К сожалению карты не найдены в базе');
$html->set('amaps', $mapsjs);
$html->set('pages', $html->arr['pages'] ?? '');
$html->pack('main');
