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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Подключение раздела
if (!in_array($section, ['cs', 'css', 'cssold', 'csgo', 'cs2', 'rust',  'mc', 'mta', 'samp', 'crmp', 'hosting', 'privileges'])) {
    $title = 'Список услуг';
    $html->nav($title);

    $html->get('index', 'sections/services');
    $html->pack('main');
} else {
    $aNav = [
        'cs' => 'Counter-Srike: 1.6',
        'css' => 'Counter-Srike: Source',
        'cssold' => 'Counter-Srike: Source v34',
        'csgo' => 'Counter-Srike: Global Offensive',
        'cs2' => 'Counter-Srike: 2',
        'rust' => 'RUST',
        'mc' => 'Minecraft',
        'mta' => 'GTA: MTA',
        'samp' => 'GTA: SA-MP',
        'crmp' => 'GTA: CR-MP',
        'hosting' => 'виртуального хостинга',
        'privileges' => 'привилегий на игровом сервере',
    ];

    $title = 'Аренда ' . $aNav[$section];

    $html->nav('Список услуг', $cfg['http'] . 'services');
    $html->nav($title);

    include(SEC . 'services/' . $section . '.php');
}
