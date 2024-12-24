<?php

/*
 * Copyright 2018-2024 Solovev Sergei
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

include(LIB . 'geo.php');
$SxGeo = new SxGeo(DATA . 'SxGeoCity.dat');

// Генерация списка авторизаций
$qBro = $sql->query('SELECT `ip`, `date`, `browser` FROM `auth` WHERE `user`="' . $user['id'] . '" ORDER BY `id` DESC LIMIT 20');
while ($aBro = $sql->get($qBro)) {
    $browser = base64_decode($aBro['browser']);

    $cData = $SxGeo->getCityFull($aBro['ip']);

    if ($cData && isset($cData['country']['iso'])) {
        $ico = sys::country($cData['country']['iso']);
    } else {
        $ico = sys::country('none');
    }

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

$html->set('auth', $html->arr['auth'] ?? '', true);

$html->pack('main');
