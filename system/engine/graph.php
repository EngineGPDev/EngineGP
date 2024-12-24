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

$key = $url['key'] ?? exit;

if (sys::valid($key, 'md5')) {
    exit;
}

$sql->query('SELECT `id`, `server`, `time` FROM `graph` WHERE `key`="' . $key . '" LIMIT 1');

if (!$sql->num()) {
    exit;
}

$graph = $sql->get();

if (isset($url['type'])) {
    include(DATA . 'graph.php');

    include(LIB . 'games/graph.php');

    $style = $url['style'] ?? 'default';

    if (!array_key_exists($style, $aStyle)) {
        $style = 'default';
    }

    $type = $url['type'] ?? 'first';

    if (!in_array($type, ['first', 'second'])) {
        $type = 'first';
    }

    // Выхлоп кеш баннера
    if (file_exists(TEMP . (md5($key . $style . $type)) . '.png') and $graph['time'] + 300 > $start_point) {
        header('Content-type: image/png');
        exit(file_get_contents(TEMP . (md5($key . $style . $type)) . '.png'));
    }

    $sql->query('SELECT `address`, `port`, `game`, `slots_start`, `online`, `status`, `name`, `map` FROM `servers` WHERE `id`="' . $graph['server'] . '" LIMIT 1');
    if (!$sql->num()) {
        exit;
    }

    $server = $sql->get();

    $aPoints = graph::online_day($graph['server'], $server['slots_start']);

    // Обновление баннеров
    foreach ($aStyle as $name => $styles) {
        graph::first($server, $aPoints, $aStyle, $name, $key);
        graph::second($server, $aPoints, $aStyle, $name, $key);
    }

    // Обновление данных
    $sql->query('UPDATE `graph` set `time`="' . $start_point . '" WHERE `id`="' . $graph['id'] . '" LIMIT 1');

    header('Content-type: image/png');
    exit(file_get_contents(TEMP . (md5($key . $style . $type)) . '.png'));
}
