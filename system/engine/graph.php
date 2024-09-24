<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$key = isset($url['key']) ? $url['key'] : exit;

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

    $style = isset($url['style']) ? $url['style'] : 'default';

    if (!array_key_exists($style, $aStyle)) {
        $style = 'default';
    }

    $type = isset($url['type']) ? $url['type'] : 'first';

    if (!in_array($type, array('first', 'second'))) {
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
