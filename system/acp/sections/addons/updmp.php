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

if (isset($url['get']) and $url['get'] == 'list') {
    $unit = isset($url['unit']) ? AdminSystem::int($url['unit']) : AdminSystem::out();
    $game = $url['game'] ?? AdminSystem::out();

    if (!in_array($game, ['cs', 'cssold', 'css', 'csgo', 'cs2'])) {
        AdminSystem::out();
    }

    $maps = '';

    $sql->query('SELECT `name` FROM `maps` WHERE `unit`="' . $unit . '" AND `game`="' . $game . '" ORDER BY `id` ASC');

    $all = 'Общее число карт: ' . $sql->num() . ' шт.' . PHP_EOL;

    while ($map = $sql->get()) {
        $maps .= $map['name'] . PHP_EOL;
    }

    $maps = $maps == '' ? 'В базе нет карт' : $all . $maps . $all;

    AdminSystem::out($maps);
}

if ($go) {
    $unit = isset($url['unit']) ? AdminSystem::int($url['unit']) : AdminSystem::outjs(['e' => 'Необходимо выбрать локацию']);
    $game = $url['game'] ?? AdminSystem::outjs(['e' => 'Необходимо выбрать игру']);

    if (!$unit) {
        AdminSystem::outjs(['e' => 'Необходимо выбрать локацию']);
    }

    if (!in_array($game, ['cs', 'cssold', 'css', 'csgo', 'cs2'])) {
        AdminSystem::outjs(['e' => 'Необходимо выбрать игру']);
    }

    include(LIB . 'ssh.php');

    $sql->query('SELECT `id`, `passwd`, `address` FROM `units` WHERE `id`="' . $unit . '" LIMIT 1');
    if (!$sql->num()) {
        AdminSystem::outjs(['e' => 'Локация не найдена']);
    }

    $unit = $sql->get();

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        AdminSystem::outjs(['e' => 'Не удалось создать связь с локацией']);
    }

    $sql->query('DELETE FROM `maps` WHERE `unit`="' . $unit['id'] . '" AND `game`="' . $game . '"');

    $maps = $ssh->get('cd /path/maps/' . $game . ' && ls | grep .bsp | grep -v .bsp.');

    $aMaps = explode("\n", $maps);

    array_pop($aMaps);

    foreach ($aMaps as $map) {
        $aMapParts = explode('.', $map);
        $name = array_shift($aMapParts);

        $sql->query('INSERT INTO `maps` set `unit`="' . $unit['id'] . '", `game`="' . $game . '", `name`="' . $name . '"');
    }

    AdminSystem::outjs(['s' => 'ok']);
}

$units = '';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get()) {
    $units .= '<option value="' . $unit['id'] . '">' . $unit['name'] . '</option>';
}

$html->get('updmp', 'sections/addons');

$html->set('units', $units);

$html->pack('main');
