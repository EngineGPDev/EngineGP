<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Установка
if ($go) {
    require(LIB . 'web/free.php');

    $aData = [];

    $aData['subdomain'] = isset($_POST['subdomain']) ? strtolower((string) $_POST['subdomain']) : sys::outjs(['e' => 'Необходимо указать адрес.'], $nmch);
    $aData['domain'] = isset($_POST['domain']) ? strtolower((string) $_POST['domain']) : sys::outjs(['e' => 'Необходимо выбрать домен.'], $nmch);
    $aData['desing'] = isset($_POST['desing']) ? strtolower((string) $_POST['desing']) : sys::outjs(['e' => 'Необходимо выбрать шаблон.'], $nmch);

    $aData['type'] = $url['subsection'];
    $aData['server'] = array_merge($server, ['id' => $id]);

    $aData['config_sql'] = '';
    $aData['config_php'] = '';

    web::install($aData, $nmch);
}

$html->nav('Установка ' . $aWebname[$url['subsection']]);

$desing = '';

// Генерация списка шаблонов
foreach ($aWebParam[$url['subsection']]['desing'] as $name => $desc)
    $desing .= '<option value="' . $name . '">' . $desc . '</option>';

$domains = '';

// Генерация списка доменов
foreach ($aWebUnit['domains'] as $domain)
    $domains .= '<option value="' . $domain . '">.' . $domain . '</option>';

$html->get('install', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->set('desing', $desing);
$html->set('domains', $domains);

$html->pack('main');
