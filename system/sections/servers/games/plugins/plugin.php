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

$pid = isset($url['plugin']) ? sys::int($url['plugin']) : sys::back($cfg['http'] . 'servers/id/' . $id . '/section/plugins');

$sql->query('SELECT `id`, `upd` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $pid . '" LIMIT 1');

if (!$sql->num()) {
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/plugins');
}

$install = $sql->get();

// Если установленно обновление
if ($install['upd']) {
    $sql->query('SELECT `name`, `info`, `images`, `upd` FROM `plugins_update` WHERE `id`="' . $install['upd'] . '" LIMIT 1');
} else {
    $sql->query('SELECT `name`, `info`, `images`, `upd` FROM `plugins` WHERE `id`="' . $pid . '" LIMIT 1');
}

if (!$sql->num()) {
    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/plugins');
}

$plugin = $sql->get();

$html->nav('Плагины', $cfg['http'] . 'servers/id/' . $id . '/section/plugins');
$html->nav($plugin['name']);

// Если есть кеш
if ($mcache->get('server_plugin_' . $pid . $id) != '') {
    $html->arr['main'] = $mcache->get('server_plugin_' . $pid . $id);
} else {
    include(LIB . 'games/plugins.php');

    // Построение списка редактируемых файлов
    $aConf = [];

    $sql->query('SELECT `id`, `file` FROM `plugins_config` WHERE (`plugin`="' . $pid . '" AND `update`="0") OR (`plugin`="' . $pid . '" AND `update`="' . $install['upd'] . '") ORDER BY `sort`, `id` ASC');
    while ($config = $sql->get()) {
        // Исключить дублирование, путем проверки массива файлов
        if (in_array($config['file'], $aConf)) {
            continue;
        }

        $aConf[] = $config['file'];

        // Данные файла
        $file = explode('/', $config['file']);

        $html->get('config_list', 'sections/servers/games/plugins');

        $html->set('id', $id);
        $html->set('fid', $config['id']);
        $html->set('name', end($file));
        $html->set('file', $config['file']);

        $html->pack('configs');
    }

    $images = plugins::images($plugin['images'], $pid);

    $html->get('configs', 'sections/servers/games/plugins');

    $html->set('id', $id);
    $html->set('name', $plugin['name']);
    $html->set('info', htmlspecialchars_decode($plugin['info']));

    // Картинки
    if (!empty($images)) {
        $html->unit('images', 1);
        $html->set('images', $images);
    } else {
        $html->unit('images');
    }

    // Редактируемые файлы
    if (isset($html->arr['configs'])) {
        $html->set('configs', $html->arr['configs']);
        $html->unit('configs', 1);
    } else {
        $html->unit('configs');
    }

    $html->pack('main');

    $mcache->set('server_plugin_' . $pid . $id, $html->arr['main'], false, 60);
}
