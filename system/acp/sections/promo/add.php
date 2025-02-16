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

use EngineGP\AdminSystem;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if ($go) {
    $aData = [];

    $aData['cod'] = isset($_POST['cod']) ? trim($_POST['cod']) : '';
    $aData['value'] = isset($_POST['value']) ? trim($_POST['value']) : '';
    $aData['discount'] = isset($_POST['discount']) ? AdminSystem::int($_POST['discount']) : 0;
    $aData['hits'] = isset($_POST['hits']) ? AdminSystem::int($_POST['hits']) : '';
    $aData['use'] = isset($_POST['use']) ? AdminSystem::int($_POST['use']) : '';
    $aData['extend'] = isset($_POST['extend']) ? AdminSystem::int($_POST['extend']) : 0;
    $aData['user'] = isset($_POST['user']) ? AdminSystem::int($_POST['user']) : '';
    $aData['server'] = isset($_POST['server']) ? AdminSystem::int($_POST['server']) : '';
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : '';
    $aData['data'] = isset($_POST['data']) ? trim($_POST['data']) : '';
    $aData['tarifs'] = $_POST['tarifs'] ?? '';

    $aData['time'] = AdminSystem::checkdate($aData['time']);

    if (AdminSystem::valid($aData['cod'], 'promo')) {
        AdminSystem::outjs(['e' => 'Неправильный формат промо-кода']);
    }

    if ($aData['user']) {
        $sql->query('SELECT `id` FROM `users` WHERE `id`="' . $aData['user'] . '" LIMIT 1');
        if (!$sql->num()) {
            AdminSystem::outjs(['e' => 'Указанный пользователь не найден']);
        }
    } else {
        $aData['user'] = 0;
    }

    if ($aData['server']) {
        $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $aData['server'] . '" LIMIT 1');
        if (!$sql->num()) {
            AdminSystem::outjs(['e' => 'Указанный сервер не найден']);
        }
    } else {
        $aData['server'] = 0;
    }

    if (!is_array($aData['tarifs']) || !count($aData['tarifs'])) {
        AdminSystem::outjs(['e' => 'Необходимо указать минимум один тариф']);
    }

    if ($aData['discount']) {
        $proc = strpos($aData['value'], '%') ? '%' : '';
    }

    $aData['value'] = AdminSystem::int($aData['value']) . $proc;

    foreach ($aData['tarifs'] as $id => $on) {
        $sql->query('SELECT `id` FROM `promo` WHERE `cod`="' . $aData['cod'] . '" AND `tarif`="' . $id . '" LIMIT 1');
        if ($sql->num()) {
            continue;
        }

        $sql->query('INSERT INTO `promo` set '
            . '`cod`="' . $aData['cod'] . '",'
            . '`value`="' . $aData['value'] . '",'
            . '`discount`="' . $aData['discount'] . '",'
            . '`data`="' . base64_encode('{' . $aData['data'] . '}') . '",'
            . '`hits`="' . $aData['hits'] . '",'
            . '`use`="' . $aData['use'] . '",'
            . '`extend`="' . $aData['extend'] . '",'
            . '`tarif`="' . $id . '",'
            . '`user`="' . $aData['user'] . '",'
            . '`server`="' . $aData['server'] . '",'
            . '`time`="' . $aData['time'] . '"');
    }

    AdminSystem::outjs(['s' => 'ok']);
}

$tarifs = '';

$units = $sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
while ($unit = $sql->get($units)) {
    $sql->query('SELECT `id`, `name`, `game` FROM `tarifs` WHERE `unit`="' . $unit['id'] . '" ORDER BY `id` ASC');
    while ($tarif = $sql->get()) {
        $tarifs .= '<label> ' . $unit['name'] . ' / #' . $tarif['id'] . ' ' . $tarif['name'] . ' (' . strtoupper($tarif['game']) . ') <input type="checkbox" name="tarifs[' . $tarif['id'] . ']"></label>';
    }
}

$html->get('add', 'sections/promo');

$html->set('tarifs', $tarifs);

$html->pack('main');
