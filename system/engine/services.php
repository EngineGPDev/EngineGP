<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Подключение раздела
if (!in_array($section, array('cs', 'css', 'cssold', 'csgo', 'cs2', 'rust',  'mc', 'mta', 'samp', 'crmp', 'hosting', 'privileges', 'control'))) {
    $title = 'Список услуг';
    $html->nav($title);

    $html->get('index', 'sections/services');
    $html->pack('main');
} else {
    $aNav = array(
        'cs' => 'Counter-Srike: 1.6',
        'css' => 'Counter-Srike: Source',
        'cssold' => 'Counter-Srike: Source v34',
        'csgo' => 'Counter-Srike: Global Offensive',
        'cs2' => 'Counter-Srike: 2',
        'rust' => 'RUST',
        'mc' => 'MineCraft',
        'mta' => 'GTA: MTA',
        'samp' => 'GTA: SA-MP',
        'crmp' => 'GTA: CR-MP',
        'hosting' => 'виртуального хостинга',
        'privileges' => 'привилегий на игровом сервере',
        'control' => 'услуги "контроль"',
    );

    $title = 'Аренда ' . $aNav[$section];

    $html->nav('Список услуг', $cfg['http'] . 'services');
    $html->nav($title);

    include(SEC . 'services/' . $section . '.php');
}
