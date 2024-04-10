<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

include(LIB . 'geo.php');
$SxGeo = new SxGeo(DATA . 'SxGeoCity.dat');

// Генерация списка авторизаций
$qBro = $sql->query('SELECT `ip`, `date`, `browser` FROM `auth` WHERE `user`="' . $user['id'] . '" ORDER BY `id` DESC LIMIT 20');
while ($aBro = $sql->get($qBro)) {
    $browser = base64_decode($aBro['browser']);

    $cData = $SxGeo->getCityFull($aBro['ip']);
    $ico = sys::country($cData['country']['iso']);

    $html->get('list', 'sections/user/lk/auth');

    $html->set('ip', $aBro['ip']);
    $html->set('date', sys::today($aBro['date'], true));
    $html->set('browser', sys::browser($browser));
    $html->set('more', $browser);
    $html->set('flag', $ico);
    $html->set('country', empty($cData['country']['name_ru']) ? 'Не определена' : $cData['country']['name_ru']);

    $html->pack('auth');
}

$html->get('auth', 'sections/user/lk');

$html->set('auth', isset($html->arr['auth']) ? $html->arr['auth'] : '', true);

$html->pack('main');
