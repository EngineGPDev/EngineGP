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

$sql->query('SELECT `unit`, `tarif`, `pack` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);

// Подразделы
$aSub = ['install', 'delete', 'update', 'plugin', 'config', 'search'];

// Если выбран подраздел
if (isset($url['subsection']) and in_array($url['subsection'], $aSub)) {
    $html->nav('Плагины', $cfg['http'] . 'servers/id/' . $id . '/section/plugins');

    $nmch = System::rep_act('server_plugins_go_' . $id, 10);

    include(SEC . 'servers/games/plugins/' . $url['subsection'] . '.php');
} else {
    $html->nav('Плагины');

    // Если есть кеш
    if ($mcache->get('server_plugins_' . $id) != '') {
        $html->arr['main'] = $mcache->get('server_plugins_' . $id);
    } else {
        include(LIB . 'games/plugins.php');

        // Категории
        $cats = $sql->query('SELECT `id`, `name` FROM `plugins_category` WHERE `game`="' . $server['game'] . '" ORDER BY `sort` ASC');
        while ($cat = $sql->get($cats)) {
            // Плагины
            $plugins = $sql->query('SELECT `id`, `name`, `desc`, `images`, `status`, `upd`, `packs`, `price` FROM `plugins` WHERE `cat`="' . $cat['id'] . '" ORDER BY `sort`, `id` ASC');
            while ($plugin = $sql->get($plugins)) {
                // Проверка, установлен ли плагин на сервер
                $sql->query('SELECT `id` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $plugin['id'] . '" LIMIT 1');
                if ($sql->num()) {
                    continue;
                }

                // Проверка наличия обновленной версии плагина
                if ($plugin['upd']) {
                    $idp = $plugin['id'];

                    $sql->query('SELECT `name`, `desc`, `images`, `status`, `packs`, `price` FROM `plugins_update` WHERE `plugin`="' . $plugin['id'] . '" ORDER BY `id` DESC LIMIT 1');
                    if ($sql->num()) {
                        $plugin = $sql->get();

                        $plugin['id'] = $idp;
                    } else {
                        $plugin['upd'] = 0;
                    }
                }

                // Проверка на доступность плагина к установленной на сервере сборке
                $packs = strpos($plugin['packs'], ':') ? explode(':', $plugin['packs']) : [$plugin['packs']];
                if (!in_array($server['pack'], $packs) and $plugin['packs'] != 'all') {
                    continue;
                }

                $images = plugins::images($plugin['images'], $plugin['id']);

                $buy = null;

                if ($plugin['price']) {
                    $sql->query('SELECT `id` FROM `plugins_buy` WHERE `plugin`="' . $plugin['id'] . '" AND `server`="' . $id . '" LIMIT 1');
                    $buy = $sql->num();
                }

                // Шаблон плагина
                $html->get('plugin', 'sections/servers/games/plugins');

                $html->set('id', $id);
                $html->set('plugin', $plugin['id']);

                plugins::status($plugin['status']);

                $html->set('name', htmlspecialchars_decode($plugin['name']));
                $html->set('desc', htmlspecialchars_decode($plugin['desc']));

                if (!empty($images)) {
                    $html->unit('images', 1);
                    $html->set('images', $images);
                } else {
                    $html->unit('images');
                }

                if (!$buy and $plugin['price']) {
                    $html->unit('price', true, true);
                    $html->set('price', $plugin['price']);
                } else {
                    $html->unit('price', false, true);
                }

                $html->pack('plugins');
            }

            // Шаблон блока плагинов
            $html->get('category', 'sections/servers/games/plugins');

            $html->set('name', $cat['name']);
            $html->set('plugins', $html->arr['plugins'] ?? 'Доступных для установки плагинов нет.', 1);

            $html->pack('addons');
        }

        unset($cats, $cat, $plugins, $plugin);

        // Список установленных плагинов на сервер (отдельный блок)
        $pl_ins = $sql->query('SELECT `plugin`, `upd`, `time` FROM `plugins_install` WHERE `server`="' . $id . '" ORDER BY `plugin`');
        while ($plugin = $sql->get($pl_ins)) {
            $sql->query('SELECT `id` FROM `plugins` WHERE `id`="' . $plugin['plugin'] . '" LIMIT 1');
            if (!$sql->num()) {
                continue;
            }

            $isUpd = $plugin['upd'];

            // Если установлен обновленный плагин
            if ($isUpd) {
                $sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins_update` WHERE `id`="' . $isUpd . '" LIMIT 1');
            } else {
                $sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins` WHERE `id`="' . $plugin['plugin'] . '" LIMIT 1');
            }

            $plugin = array_merge($plugin, $sql->get());

            // Шаблон плагина
            $html->get('plugin_install', 'sections/servers/games/plugins');

            $html->set('id', $id);
            $html->set('plugin', $plugin['plugin']);

            plugins::status($plugin['status']);

            if ($plugin['cfg']) {
                $html->unit('config', 1);
            } else {
                $html->unit('config');
            }

            if ($plugin['upd']) {
                $html->unit('update', 1);
            } else {
                $html->unit('update');
            }

            $html->set('name', htmlspecialchars_decode($plugin['name']));
            $html->set('time', System::today($plugin['time']));
            $html->set('desc', htmlspecialchars_decode($plugin['desc']));

            $html->pack('install');
        }

        $html->get('plugins', 'sections/servers/games');

        $html->set('id', $id);
        $html->set('addons', $html->arr['addons'] ?? '');
        $html->set('install', $html->arr['install'] ?? 'Установленные плагины отсутствуют.');

        $html->pack('main');

        $mcache->set('server_plugins_' . $id, $html->arr['main'], false, 60);
    }
}
