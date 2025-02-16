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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$sql->query('SELECT `security_ip`, `security_code` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
$user = array_merge($user, $sql->get());

// Выполнений действий
if (isset($url['action']) and in_array($url['action'], ['on', 'off', 'on_code', 'off_code', 'add', 'del', 'info'])) {
    $snw = isset($_POST['subnetwork']) ? true : false;

    switch ($url['action']) {
        case 'on':
            $sql->query('UPDATE `users` set `security_ip`="1" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            System::outjs(['s' => 'ok']);

            // no break
        case 'off':
            $sql->query('UPDATE `users` set `security_ip`="0" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            System::outjs(['s' => 'ok']);

            // no break
        case 'on_code':
            $sql->query('UPDATE `users` set `security_code`="1" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            System::outjs(['s' => 'ok']);

            // no break
        case 'off_code':
            $sql->query('UPDATE `users` set `security_code`="0" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            System::outjs(['s' => 'ok']);

            // no break
        case 'add':
            $address = isset($_POST['address']) ? trim($_POST['address']) : exit();

            if (System::valid($address, 'ip')) {
                System::outjs(['e' => 'Указанный адрес имеет неверный формат.']);
            }

            // Если подсеть
            if ($snw) {
                $address = System::whois($address);

                if ($address == 'не определена') {
                    System::outjs(['e' => 'Не удалось определить подсеть для указанного адреса.']);
                }
            }

            $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $address . '" LIMIT 1');

            // Если такой адрес уже добавлен
            if ($sql->num()) {
                System::outjs(['s' => 'ok']);
            }

            $sql->query('INSERT INTO `security` set `user`="' . $user['id'] . '", `address`="' . $address . '", `time`="' . $start_point . '"');

            System::outjs(['s' => 'ok']);

        case 'del':
            $address = isset($_POST['address']) ? trim($_POST['address']) : exit();

            if (!is_numeric($address) and System::valid($address, 'ip')) {
                System::outjs(['e' => System::outjs(['e' => 'Указанный адрес имеет неверный формат.'])]);
            }

            if (is_numeric($address)) {
                $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `id`="' . $address . '" LIMIT 1');

                // Если такое правило отсутствует
                if (!$sql->num()) {
                    System::outjs(['s' => 'ok']);
                }
            } else {
                $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $address . '" LIMIT 1');

                // Если одиночный адрес не найден, проверить на разрешенную подсеть
                if (!$sql->num()) {
                    $address = System::whois($address);

                    $sql->query('SELECT `id` FROM `security` WHERE `user`="' . $user['id'] . '" AND `address`="' . $address . '" LIMIT 1');

                    if ($sql->num()) {
                        $security = $sql->get();

                        System::outjs(['i' => 'Указанный адрес входит в разрешенную подсеть, удалить подсеть?', 'id' => $security['id']]);
                    }

                    System::outjs(['s' => 'ok']);
                }
            }

            $security = $sql->get();

            $sql->query('DELETE FROM `security` WHERE `id`="' . $security['id'] . '" LIMIT 1');

            System::outjs(['s' => 'ok']);

        case 'info':
            $address = isset($_POST['address']) ? trim($_POST['address']) : System::outjs(['info' => 'Не удалось получить информацию.']);

            if (System::valid($address, 'ip')) {
                System::outjs(['e' => 'Указанный адрес имеет неверный формат.']);
            }

            include(LIB . 'geo.php');

            $SxGeo = new SxGeo(DATA . 'SxGeoCity.dat');

            $data = $SxGeo->getCityFull($address);

            $info = 'Информация об IP адресе:';

            if ($data['country']['name_ru'] != '') {
                $info .= '<p>Страна: ' . $data['country']['name_ru'];

                if ($data['city']['name_ru'] != '') {
                    $info .= '<p>Город: ' . $data['city']['name_ru'];
                }

                $info .= '<p>Подсеть: ' . System::whois($address);

            } else {
                $info = 'Не удалось получить информацию.';
            }

            System::outjs(['info' => $info]);
    }
}

// Построение списка добавленных адресов
$sql->query('SELECT `id`, `address` FROM `security` WHERE `user`="' . $user['id'] . '" ORDER BY `id` ASC');
while ($security = $sql->get()) {
    $html->get('list', 'sections/user/lk/security');

    $html->set('id', $security['id']);
    $html->set('address', $security['address']);

    $html->pack('security');
}

$html->get('security', 'sections/user/lk');

$html->set('ip', $uip);
$html->set('subnetwork', System::whois($uip));

$html->set('security', $html->arr['security'] ?? '', true);

if ($user['security_ip']) {
    $html->unit('security_ip', true, true);
} else {
    $html->unit('security_ip', false, true);
}

if ($user['security_code']) {
    $html->unit('security_code', true, true);
} else {
    $html->unit('security_code', false, true);
}

$html->pack('main');
