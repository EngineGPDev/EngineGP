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

$html->nav('Блокировка на оборудовании');

if (isset($url['action'])) {
    include(LIB . 'games/games.php');

    // Получение информации адреса
    if ($url['action'] == 'info') {
        games::iptables_whois($nmch);
    }

    // Добавление / удаление правил
    if ($go && in_array($url['action'], ['block', 'unblock'])) {
        $address = isset($_POST['address']) ? trim($_POST['address']) : sys::outjs(['e' => sys::text('servers', 'firewall')], $nmch);
        $snw = isset($_POST['subnetwork']) ? true : false;

        sys::outjs(games::iptables($id, $url['action'], $address, $server['address'], $server['port'], $server['unit'], $snw), $nmch);
    }
}

$sql->query('SELECT `id`, `sip` FROM `firewall` WHERE `server`="' . $id . '" ORDER BY `id` ASC');

while ($firewall = $sql->get()) {
    $html->get('list', 'sections/servers/games/settings/firewall');
    $html->set('id', $firewall['id']);
    $html->set('address', $firewall['sip']);
    $html->pack('firewall');
}

$html->get('firewall', 'sections/servers/games/settings');
$html->set('id', $id);
$html->set('firewall', $html->arr['firewall'] ?? '');
$html->pack('main');
