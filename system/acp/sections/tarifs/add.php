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

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['unit'] = isset($_POST['unit']) ? AdminSystem::int($_POST['unit']) : '';
    $aData['game'] = isset($_POST['game']) ? trim($_POST['game']) : '';
    $aData['slots'] = isset($_POST['slots']) ? trim($_POST['slots']) : '';
    $aData['posts'] = isset($_POST['posts']) ? trim($_POST['posts']) : '';
    $aData['hostname'] = isset($_POST['hostname']) ? trim($_POST['hostname']) : '';
    $aData['packs'] = isset($_POST['packs']) ? trim($_POST['packs']) : '';
    $aData['path'] = isset($_POST['path']) ? trim($_POST['path']) : '';
    $aData['install'] = isset($_POST['install']) ? trim($_POST['install']) : '';
    $aData['update'] = isset($_POST['update']) ? trim($_POST['update']) : '';
    $aData['fps'] = isset($_POST['fps']) ? trim($_POST['fps']) : '';
    $aData['tickrate'] = isset($_POST['tickrate']) ? trim($_POST['tickrate']) : '';
    $aData['ram'] = isset($_POST['ram']) ? trim($_POST['ram']) : '';
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : '';
    $aData['timext'] = isset($_POST['timext']) ? trim($_POST['timext']) : '';
    $aData['test'] = isset($_POST['test']) ? AdminSystem::int($_POST['test']) : '';
    $aData['tests'] = isset($_POST['tests']) ? AdminSystem::int($_POST['tests']) : '';
    $aData['discount'] = isset($_POST['discount']) ? AdminSystem::int($_POST['discount']) : '';
    $aData['map'] = isset($_POST['map']) ? trim($_POST['map']) : '';
    $aData['ftp'] = $_POST['ftp'] ?? '';
    $aData['plugins'] = $_POST['plugins'] ?? '';
    $aData['console'] = $_POST['console'] ?? '';
    $aData['stats'] = $_POST['stats'] ?? '';
    $aData['copy'] = $_POST['copy'] ?? '';
    $aData['web'] = $_POST['web'] ?? '';
    $aData['plugins_install'] = isset($_POST['plugins_install']) ? trim($_POST['plugins_install']) : '';
    $aData['hdd'] = isset($_POST['hdd']) ? AdminSystem::int($_POST['hdd']) : '';
    $aData['autostop'] = isset($_POST['autostop']) ? AdminSystem::int($_POST['autostop']) : '';
    $aData['price'] = isset($_POST['price']) ? trim($_POST['price']) : '';
    $aData['cpu'] = isset($_POST['cpu']) ? trim($_POST['cpu']) : '';
    $aData['ip'] = isset($_POST['ip']) ? trim($_POST['ip']) : '';
    $aData['show'] = isset($_POST['show']) ? AdminSystem::int($_POST['show']) : '';
    $aData['sort'] = isset($_POST['sort']) ? AdminSystem::int($_POST['sort']) : '';

    if ($aData['name'] == '') {
        AdminSystem::outjs(['e' => 'Необходимо указать название']);
    }

    $sql->query('SELECT `id` FROM `units` WHERE `id`="' . $aData['unit'] . '" LIMIT 1');
    if (!$sql->num()) {
        AdminSystem::outjs(['e' => 'Необходимо указать локацию']);
    }

    if (!in_array($aData['game'], ['cs', 'cssold', 'css', 'csgo', 'cs2', 'rust', 'samp', 'crmp', 'mta', 'mc'])) {
        AdminSystem::outjs(['e' => 'Необходимо указать игру']);
    }

    $aSlots = explode('-', $aData['slots']);

    if (!isset($aSlots[0]) || !isset($aSlots[1])) {
        AdminSystem::outjs(['e' => 'Необходимо указать слоты']);
    }

    $aSlots[0] = trim($aSlots[0]);
    $aSlots[1] = trim($aSlots[1]);

    $aData['slots_min'] = $aSlots[0] > 1 ? $aSlots[0] : AdminSystem::outjs(['e' => 'Неправильно указаны слоты']);
    $aData['slots_max'] = $aSlots[1] >= $aSlots[0] ? $aSlots[1] : AdminSystem::outjs(['e' => 'Неправильно указаны слоты']);

    $aPorts = explode('-', $aData['posts']);

    if (!isset($aPorts[0]) || !isset($aPorts[1])) {
        AdminSystem::outjs(['e' => 'Необходимо указать порты']);
    }

    $aPorts[0] = trim($aPorts[0]);
    $aPorts[1] = trim($aPorts[1]);

    $aData['port_min'] = $aPorts[0] > 1 ? $aPorts[0] : AdminSystem::outjs(['e' => 'Неправильно указаны порты']);
    $aData['port_max'] = $aPorts[1] >= $aPorts[0] ? $aPorts[1] : AdminSystem::outjs(['e' => 'Неправильно указаны порты']);

    if ($aData['hostname'] == '') {
        AdminSystem::outjs(['e' => 'Необходимо указать название сервера']);
    }

    if ($aData['path'] == '') {
        AdminSystem::outjs(['e' => 'Необходимо указать путь до сборок']);
    }

    if ($aData['install'] == '') {
        AdminSystem::outjs(['e' => 'Необходимо указать путь для установки серверов']);
    }

    if ($aData['update'] == '') {
        AdminSystem::outjs(['e' => 'Необходимо указать путь до обновления сборки']);
    }

    if (substr($aData['path'], -1) != '/' || substr($aData['install'], -1) != '/' || substr($aData['update'], -1) != '/') {
        AdminSystem::outjs(['e' => 'Пути должны заканчиваться символом "/"']);
    }

    $int = [
        'Тестов' => 'tests',
        'Положение' => 'sort',
        'Диск' => 'hdd',
    ];

    foreach ($int as $name => $input) {
        if ($aData[$input] == '') {
            AdminSystem::outjs(['e' => 'Необходимо указать поле "' . $name . '"']);
        }
    }

    $aPacks = explode(',', $aData['packs']);

    $packs = [];

    foreach ($aPacks as $pack) {
        $aPack = explode(':', trim($pack));

        if (!isset($aPack[0]) || !isset($aPack[1])) {
            continue;
        }

        $name = str_replace('"', '', $aPack[0]);
        $fullname = str_replace('"', '', $aPack[1]);

        $packs[trim($name)] = trim($fullname);
    }

    if (!count($packs)) {
        AdminSystem::outjs(['e' => 'Необходимо указать минимум одну сборку']);
    }

    $aData['packs'] = AdminSystem::b64js($packs);

    $aIp = explode(':', $aData['ip']);

    $ips = '';

    foreach ($aIp as $ip) {
        $ip = trim($ip);

        if (AdminSystem::valid($ip, 'ip')) {
            continue;
        }

        $ips .= $ip . ':';
    }

    $ips = isset($ips[0]) ? substr($ips, 0, -1) : '';

    $aData['ip'] = $ips;

    $aPlugins = explode('","', $aData['plugins_install']);

    $plugins = [];

    foreach ($aPlugins as $plugin) {
        $aPlugin = explode(':', trim($plugin));

        if (!isset($aPlugin[0]) || !isset($aPlugin[1])) {
            continue;
        }

        $name = trim(str_replace('"', '', $aPlugin[0]));

        if (!isset($packs[$name])) {
            continue;
        }

        $aList = explode(',', str_replace('"', '', $aPlugin[1]));

        $list = '';

        foreach ($aList as $pid) {
            $pid = trim($pid);

            if (!is_numeric($pid)) {
                continue;
            }

            $list .= intval($pid) . ',';
        }

        $list = isset($list[0]) ? substr($list, 0, -1) : '';

        if ($list == '') {
            continue;
        }

        $plugins[$name] = $list;
    }

    $aData['plugins_install'] = count($plugins) ? AdminSystem::b64js($plugins) : '';

    $aTime = explode(':', $aData['time']);

    $times = '';

    foreach ($aTime as $time) {
        $time = trim($time);

        if (!is_numeric($time)) {
            continue;
        }

        $times .= intval($time) . ':';
    }

    $times = isset($times[0]) ? substr($times, 0, -1) : '';

    $aData['time'] = $times;

    $aTimext = explode(':', $aData['timext']);

    $timexts = '';

    foreach ($aTimext as $timext) {
        $timext = trim($timext);

        if (!is_numeric($timext)) {
            continue;
        }

        $timexts .= intval($timext) . ':';
    }

    $timexts = isset($timexts[0]) ? substr($timexts, 0, -1) : '';

    $aData['timext'] = $timexts;

    $aFps = explode(':', $aData['fps']);

    $sfps = '';

    foreach ($aFps as $fps) {
        $fps = trim($fps);

        if (!is_numeric($fps)) {
            continue;
        }

        $sfps .= intval($fps) . ':';
    }

    $sfps = isset($sfps[0]) ? substr($sfps, 0, -1) : '';

    $aData['fps'] = $sfps;

    $aTick = explode(':', $aData['tickrate']);

    $stick = '';

    foreach ($aTick as $tick) {
        $tick = trim($tick);

        if (!is_numeric($tick)) {
            continue;
        }

        $stick .= intval($tick) . ':';
    }

    $stick = isset($stick[0]) ? substr($stick, 0, -1) : '';

    $aData['tickrate'] = $stick;

    $aRam = explode(':', $aData['ram']);

    $sram = '';

    foreach ($aRam as $ram) {
        $ram = trim($ram);

        if (!is_numeric($ram)) {
            continue;
        }

        $sram .= intval($ram) . ':';
    }

    $sram = isset($sram[0]) ? substr($sram, 0, -1) : '';

    $aData['ram'] = $sram;

    $aPrice = explode(':', $aData['price']);

    $sprice = '';

    foreach ($aPrice as $price) {
        $price = trim($price);

        if (!is_numeric($price)) {
            continue;
        }

        $sprice .= $price . ':';
    }

    $sprice = isset($sprice[0]) ? substr($sprice, 0, -1) : '';

    $aData['price'] = $sprice;

    switch ($aData['game']) {
        case 'cs':
            if (count(explode(':', $aData['fps'])) != count(explode(':', $aData['price']))) {
                AdminSystem::outjs(['e' => 'Неправильно указано поле "Цена"']);
            }

            break;

        case 'cssold':
            $afps = explode(':', $aData['fps']);
            $atick = explode(':', $aData['tickrate']);
            $aprice = explode(':', $aData['price']);

            if ((count($afps) * count($atick)) != count($aprice)) {
                AdminSystem::outjs(['e' => 'Неправильно указано поле "Цена"']);
            }

            $price = [];

            $i = 0;

            foreach ($afps as $fps) {
                foreach ($atick as $tick) {
                    $price[$tick . '_' . $fps] = $aprice[$i];

                    $i += 1;
                }
            }

            $aData['price'] = AdminSystem::b64js($price);

            break;

        case 'css':
        case 'cs2':
        case 'csgo':
            if (count(explode(':', $aData['tickrate'])) != count(explode(':', $aData['price']))) {
                AdminSystem::outjs(['e' => 'Неправильно указано поле "Цена"']);
            }

            break;

        case 'mc':
            if (count(explode(':', $aData['ram'])) != count(explode(':', $aData['price']))) {
                AdminSystem::outjs(['e' => 'Неправильно указано поле "Цена"']);
            }

    }

    $access = ['ftp', 'plugins', 'console', 'stats', 'copy', 'web'];

    foreach ($access as $section) {
        $aData[$section] = (string)$aData[$section] == 'on' ? '1' : '0';
    }

    $sql->query('INSERT INTO `tarifs` set'
        . '`unit`="' . $aData['unit'] . '",'
        . '`game`="' . $aData['game'] . '",'
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`slots_min`="' . $aData['slots_min'] . '",'
        . '`slots_max`="' . $aData['slots_max'] . '",'
        . '`port_min`="' . $aData['port_min'] . '",'
        . '`port_max`="' . $aData['port_max'] . '",'
        . '`hostname`="' . htmlspecialchars($aData['hostname']) . '",'
        . '`packs`="' . $aData['packs'] . '",'
        . '`path`="' . addslashes($aData['path']) . '",'
        . '`install`="' . addslashes($aData['install']) . '",'
        . '`update`="' . addslashes($aData['update']) . '",'
        . '`fps`="' . $aData['fps'] . '",'
        . '`tickrate`="' . $aData['tickrate'] . '",'
        . '`ram`="' . $aData['ram'] . '",'
        . '`time`="' . $aData['time'] . '",'
        . '`timext`="' . $aData['timext'] . '",'
        . '`test`="' . $aData['test'] . '",'
        . '`tests`="' . $aData['tests'] . '",'
        . '`discount`="' . $aData['discount'] . '",'
        . '`map`="' . addslashes($aData['map']) . '",'
        . '`ftp`="' . $aData['ftp'] . '",'
        . '`plugins`="' . $aData['plugins'] . '",'
        . '`console`="' . $aData['console'] . '",'
        . '`stats`="' . $aData['stats'] . '",'
        . '`copy`="' . $aData['copy'] . '",'
        . '`web`="' . $aData['web'] . '",'
        . '`plugins_install`="' . $aData['plugins_install'] . '",'
        . '`hdd`="' . $aData['hdd'] . '",'
        . '`autostop`="' . $aData['autostop'] . '",'
        . '`price`="' . $aData['price'] . '",'
        . '`cpu`="' . $aData['cpu'] . '",'
        . '`ip`="' . $aData['ip'] . '",'
        . '`show`="' . $aData['show'] . '",'
        . '`sort`="' . $aData['sort'] . '"');

    AdminSystem::outjs(['s' => 'ok']);
}

$units = '';

$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get()) {
    $units .= '<option value="' . $unit['id'] . '">#' . $unit['id'] . ' ' . $unit['name'] . '</option>';
}

$html->get('add', 'sections/tarifs');

$html->set('units', $units);

$html->pack('main');
