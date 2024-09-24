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

// Подключение раздела
if (!in_array($section, ['cs', 'css', 'cssold', 'csgo', 'cs2', 'rust',  'mc', 'mta', 'samp', 'crmp', 'hosting', 'privileges', 'control'])) {
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
        'control' => 'услуги "контроль"',
    ];

    $title = 'Аренда ' . $aNav[$section];

    $html->nav('Список услуг', $cfg['http'] . 'services');
    $html->nav($title);

    include(SEC . 'services/' . $section . '.php');
}
