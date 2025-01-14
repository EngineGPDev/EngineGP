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

// Массив главных разделов
$aRoute = [
    'system',
    'users',
    'units',
    'tarifs',
    'addons',
    'servers',
    'hosting',
    'web',
    'promo',
    'notice',
    'news',
    'wiki',
    'boost',
    'pages',
    'letter',
    'logs',
    'cashback',
];

// Массив регулярных выражений
$aValid = [
    'login' => '/^[A-Za-z0-9_]{4,16}$/',
    'mail' => '/^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i',
    'name' => '/^[А-ЯЁ]{1,1}[а-яё]{2,15}$/u',
    'lastname' => '/^[А-ЯЁ]{1,1}[а-яё]{2,15}$/u',
    'patronymic' => '/^[А-ЯЁ]{1,1}[а-яё]{2,15}$/u',
    'phone' => '/^380+[0-9]{9,9}$|^77+[0-9]{9,9}$|^79+[0-9]{9,9}$|^375+[0-9]{9,9}$/m',
    'contacts' => '/^(http|https):\/\/(new\.vk|vk)\.com\/[A-Za-z\_\.]{1,2}[A-Za-z0-9\_\.]{4,32}$|^[A-Za-z][A-Za-z0-9\.\-\_]{6,32}$/',
    'passwd' => '/^[A-Za-z0-9]{6,26}$/',
    'support_info' => '/[^а-яА-Я\s]+/msi',
    'address' => '/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,5}/',
];

// Массив имен игр
$aGname = [
    'cs' => 'CS: 1.6',
    'css' => 'CS: Source',
    'cssold' => 'CS: Source v34',
    'csgo' => 'CS: Global Offensive',
    'cs2' => 'CS: 2',
    'rust' => 'RUST',
    'mc' => 'Minecraft',
    'mta' => 'GTA: MTA',
    'samp' => 'GTA: SAMP',
    'crmp' => 'GTA: CRMP',
];
