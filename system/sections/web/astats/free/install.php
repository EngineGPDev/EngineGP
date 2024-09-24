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

// Установка
if ($go) {
    include(LIB . 'web/free.php');

    $aData = array();

    $aData['subdomain'] = isset($_POST['subdomain']) ? strtolower($_POST['subdomain']) : sys::outjs(array('e' => 'Необходимо указать адрес.'), $nmch);
    $aData['domain'] = isset($_POST['domain']) ? strtolower($_POST['domain']) : sys::outjs(array('e' => 'Необходимо выбрать домен.'), $nmch);
    $aData['desing'] = isset($_POST['desing']) ? strtolower($_POST['desing']) : sys::outjs(array('e' => 'Необходимо выбрать шаблон.'), $nmch);

    $aData['type'] = $url['subsection'];
    $aData['server'] = array_merge($server, array('id' => $id));

    $aData['config_sql'] = '';
    $aData['config_php'] = '';

    web::install($aData, $nmch);
}

$html->nav('Установка ' . $aWebname[$url['subsection']]);

$desing = '';

// Генерация списка шаблонов
foreach ($aWebParam[$url['subsection']]['desing'] as $name => $desc) {
    $desing .= '<option value="' . $name . '">' . $desc . '</option>';
}

$domains = '';

// Генерация списка доменов
foreach ($aWebUnit['domains'] as $domain) {
    $domains .= '<option value="' . $domain . '">.' . $domain . '</option>';
}

$html->get('install', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('desing', $desing);
$html->set('domains', $domains);

$html->pack('main');
