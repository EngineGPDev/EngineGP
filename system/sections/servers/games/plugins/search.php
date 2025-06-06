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

if (!isset($nmch)) {
    $nmch = false;
}

$text = $_POST['text'] ?? System::outjs(['none' => '']);

$mkey = md5($text . $id);

if ($mcache->get($mkey) != '') {
    System::outjs(['s' => $mcache->get($mkey)]);
}

if (!isset($text[2])) {
    System::outjs(['s' => 'Для выполнения поиска, необходимо больше данных', $nmch]);
}

$sPlugins = [];
$sUpdate = [];

// Поиск по плагинам
$plugins = $sql->query('SELECT `id`, `packs` FROM `plugins` WHERE `game`="' . $server['game'] . '" AND `name` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\') OR `desc` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\') LIMIT 5');

// Поиск по обновлениям
$update = false;

if (!$sql->num($plugins)) {
    $plugins = $sql->query('SELECT `id`, `plugin`, `packs` FROM `plugins_update` WHERE `game`="' . $server['game'] . '" AND (`name` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\') OR `desc` LIKE FROM_BASE64(\'' . base64_encode('%' . $text . '%') . '\')) AND `upd`="0" LIMIT 5');
    $update = true;
}

// Если нет ниодного совпадения по вводимому тексту
if (!$sql->num($plugins)) {
    // Поиск по словам
    if (strpos($text, ' ')) {
        // Массив слов
        $aText = explode(' ', $text);

        // Метка, которая изменится в процессе, если будет найдено хоть одно совпадение
        $sWord = false;

        foreach ($aText as $word) {
            if ($word == '' || !isset($word[2])) {
                continue;
            }

            // Поиск по плагинам
            $plugins = $sql->query('SELECT `id`, `packs` FROM `plugins` WHERE `name` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\') OR `desc` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\') LIMIT 5');

            // Поиск по обновлениям
            $update = false;

            if (!$sql->num($plugins)) {
                $plugins = $sql->query('SELECT `id`, `plugin`, `packs` FROM `plugins_update` WHERE (`name` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\') OR `desc` LIKE FROM_BASE64(\'' . base64_encode('%' . $word . '%') . '\')) AND `upd`="0" LIMIT 5');
                $update = true;
            }

            if ($sql->num($plugins)) {
                if (!$sWord) {
                    $sWord = true;
                }

                $sPlugins[] = $plugins;
                $sUpdate[] = $update;
            }
        }

        // Если нет ниодного совпадения
        if (!$sWord) {
            $mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

            System::outjs(['s' => 'По вашему запросу ничего не найдено']);
        }
    } else {
        $mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

        System::outjs(['s' => 'По вашему запросу ничего не найдено']);
    }
} else {
    $sPlugins[] = $plugins;
    $sUpdate[] = $update;
}

// Массив для исклуючения дублирования
$aPlugins = [];

foreach ($sPlugins as $index => $plugins) {
    while ($plugin = $sql->get($plugins)) {
        // Проверка дублирования
        if (($sUpdate[$index] and in_array($plugin['plugin'], $aPlugins)) || !$sUpdate[$index] and in_array($plugin['id'], $aPlugins)) {
            continue;
        }

        // Проверка на доступность плагина к установленной на сервере сборке
        $packs = strpos($plugin['packs'], ':') ? explode(':', $plugin['packs']) : [$plugin['packs']];
        if (!in_array($server['pack'], $packs) and $plugin['packs'] != 'all') {
            continue;
        }

        $install = false; // не установлен плагин
        $upd = false; // не обновлен плагин

        if ($sUpdate[$index]) {
            $sql->query('SELECT `id`, `upd`, `time` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $plugin['plugin'] . '" LIMIT 1');

            $aPlugins[] = $plugin['plugin'];
        } else {
            $sql->query('SELECT `id`, `upd`, `time` FROM `plugins_install` WHERE `server`="' . $id . '" AND `plugin`="' . $plugin['id'] . '" LIMIT 1');

            $aPlugins[] = $plugin['id'];
        }

        // Проверка на установку
        if ($sql->num()) {
            $install = $sql->get();

            $upd = $install['upd'];
            $time = System::today($install['time']);

            $install = true;
        }

        // Если установлен обновленный плагин
        if ($upd) {
            $sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins_update` WHERE `id`="' . $upd . '" LIMIT 1');
        } else {
            $sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins` WHERE `id`="' . $plugin['id'] . '" LIMIT 1');
        }

        $plugin = array_merge($plugin, $sql->get());

        $html->get('search', 'sections/servers/games/plugins');

        // Если установлен
        if ($install) {
            // Если есть обновление
            if ($plugin['upd'] > $upd) {
                $html->unit('update', 1);
            } else {
                $html->unit('update');
            }

            // Если есть редактируемые файлы
            if ($plugin['cfg']) {
                $html->unit('config', 1);
            } else {
                $html->unit('config');
            }

            $html->unit('install', 1);
            $html->unit('!install');
        } else {
            // Обновление данных на более позднею версию плагина
            $sql->query('SELECT `name`, `desc`, `status`, `cfg` FROM `plugins_update` WHERE `plugin`="' . $plugin['id'] . '" AND `upd`="0" LIMIT 1');
            if ($sql->num()) {
                $upd = $sql->get();

                $plugin['name'] = $upd['name'];
                $plugin['desc'] = $upd['desc'];
                $plugin['status'] = $upd['status'];
                $plugin['cfg'] = $upd['cfg'];
            }

            $html->unit('install');
            $html->unit('!install', 1);
        }

        if (!$plugin['status']) {
            $html->unit('unstable');
            $html->unit('stable', 1);
            $html->unit('testing');
        } elseif ($plugin['status'] == 2) {
            $html->unit('unstable');
            $html->unit('stable');
            $html->unit('testing', 1);
        } else {
            $html->unit('unstable', 1);
            $html->unit('stable');
            $html->unit('testing');
        }

        $html->set('id', $id);
        $html->set('plugin', $plugin['id']);

        if ($install) {
            $html->set('time', $time);
        }

        $html->set('name', System::find(htmlspecialchars_decode($plugin['name']), $text));
        $html->set('desc', System::find(htmlspecialchars_decode($plugin['desc']), $text));

        $html->pack('plugins');
    }
}

$html->arr['plugins'] ??= '';

$mcache->set($mkey, $html->arr['plugins'], false, 15);

System::outjs(['s' => $html->arr['plugins']], $nmch);
