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

$aGame = [
    'cs' => 'Counter-Strike: 1.6',
    'cssold' => 'Counter-Strike: Source v34',
    'css' => 'Counter-Strike: Source',
    'csgo' => 'Counter-Strike: Global Offensive',
    'cs2' => 'Counter-Strike: 2',
];

if (!isset($url['game']) || !array_key_exists($url['game'], $aGame)) {
    $url['game'] = 'cs';
}

$title = 'Доступные плагины для установки';

include(LIB . 'games/plugins.php');

if ($id) {
    $sql->query('SELECT `upd` FROM `plugins` WHERE `id`="' . $id . '" LIMIT 1');

    if (!$sql->num()) {
        System::back($cfg['http'] . 'plugins/game/' . $url['game']);
    }

    $update = $sql->get();

    $sqlq = '`name`, `info`, `desc`, `images`, `upd`';

    // Если установленно обновление
    if ($update['upd']) {
        $sql->query('SELECT ' . $sqlq . ' FROM `plugins_update` WHERE `id`="' . $update['upd'] . '" LIMIT 1');

        if (!$sql->num()) {
            $sql->query('SELECT ' . $sqlq . ' FROM `plugins` WHERE `id`="' . $id . '" LIMIT 1');
        }
    } else {
        $sql->query('SELECT ' . $sqlq . ' FROM `plugins` WHERE `id`="' . $id . '" LIMIT 1');
    }

    $plugin = $sql->get();

    $sql->query('SELECT `id`, `file` FROM `plugins_config` WHERE (`plugin`="' . $id . '" AND `update`="0") OR (`plugin`="' . $id . '" AND `update`="' . $update['upd'] . '") ORDER BY `sort`, `id` ASC');
    while ($config = $sql->get()) {
        // Исключить дублирование, путем проверки массива файлов
        if (in_array($config['file'], $aConf)) {
            continue;
        }

        $aConf[] = $config['file'];

        // Данные файла
        $file = explode('/', $config['file']);

        $html->get('config_list', 'sections/plugins');

        $html->set('game', $url['game']);
        $html->set('fid', $config['id']);
        $html->set('name', end($file));
        $html->set('file', $config['file']);

        $html->pack('configs');
    }

    $images = plugins::images($plugin['images'], $pid);

    $html->get('configs', 'sections/plugins');

    $html->set('game', $url['game']);
    $html->set('name', $plugin['name']);
    $html->set('info', htmlspecialchars_decode($plugin['info']));
    $html->set('desc', htmlspecialchars_decode($plugin['desc']));

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

    $plugin['name'] = strip_tags($plugin['name']);

    $title = $plugin['name'];
    $html->nav('Доступные плагины для установки', $cfg['http'] . 'plugins/game/' . $url['game']);
    $html->nav($plugin['name']);
}

if (!isset($html->arr['main'])) {
    $html->nav('Доступные плагины для установки');

    // Если есть кеш
    if ($mcache->get('plugins_list_view_' . $url['game']) != '') {
        $html->arr['main'] = $mcache->get('plugins_list_view_' . $url['game']);
    } else {
        // Категории
        $cats = $sql->query('SELECT `id`, `name` FROM `plugins_category` WHERE `game`="' . $url['game'] . '" ORDER BY `sort` ASC');
        while ($cat = $sql->get($cats)) {
            // Плагины
            $plugins = $sql->query('SELECT `id`, `name`, `desc`, `images`, `status`, `upd`, `packs` FROM `plugins` WHERE `cat`="' . $cat['id'] . '" ORDER BY `sort`, `id` ASC');
            while ($plugin = $sql->get($plugins)) {
                // Проверка наличия обновленной версии плагина
                if ($plugin['upd']) {
                    $idp = $plugin['id'];

                    $sql->query('SELECT `name`, `desc`, `images`, `status`, `packs` FROM `plugins_update` WHERE `plugin`="' . $plugin['id'] . '" ORDER BY `id` DESC LIMIT 1');
                    if ($sql->num()) {
                        $plugin = $sql->get();

                        $plugin['id'] = $idp;
                    } else {
                        $plugin['upd'] = 0;
                    }
                }

                $images = plugins::images($plugin['images'], $plugin['id']);

                // Шаблон плагина
                $html->get('plugin', 'sections/plugins');

                $html->set('plugin', $plugin['id']);
                $html->set('game', $url['game']);

                plugins::status($plugin['status']);

                $html->set('name', htmlspecialchars_decode($plugin['name']));
                $html->set('desc', htmlspecialchars_decode($plugin['desc']));

                if (!empty($images)) {
                    $html->unit('images', 1);
                    $html->set('images', $images);
                } else {
                    $html->unit('images');
                }

                $html->pack('plugins');
            }

            // Шаблон блока плагинов
            $html->get('category', 'sections/plugins');

            $html->set('name', $cat['name']);
            $html->set('plugins', $html->arr['plugins'] ?? 'Доступных для установки плагинов нет.', 1);

            $html->pack('addons');
        }

        $html->get('plugins', 'sections/plugins');

        $html->set('game', $aGame[$url['game']]);
        $html->set('addons', $html->arr['addons'] ?? '');

        $html->pack('main');

        $mcache->set('plugins_list_view_' . $url['game'], $html->arr['main'], false, 60);
    }
}
