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

// Массив главных разделов
$aRoute = [
    'system',
    'users',
    'units',
    'tarifs',
    'addons',
    'servers',
    'control',
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
