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

$sql->query('SELECT * FROM `promo` WHERE `id`="' . $id . '" LIMIT 1');
$promo = $sql->get();

if ($go) {
    $aData = [];

    $aData['cod'] = isset($_POST['cod']) ? trim($_POST['cod']) : $promo['cod'];
    $aData['value'] = isset($_POST['value']) ? trim($_POST['value']) : $promo['value'];
    $aData['discount'] = isset($_POST['discount']) ? AdminSystem::int($_POST['discount']) : $promo['discount'];
    $aData['hits'] = isset($_POST['hits']) ? AdminSystem::int($_POST['hits']) : $promo['hits'];
    $aData['use'] = isset($_POST['use']) ? AdminSystem::int($_POST['use']) : $promo['use'];
    $aData['extend'] = isset($_POST['extend']) ? AdminSystem::int($_POST['extend']) : $promo['extend'];
    $aData['user'] = isset($_POST['user']) ? AdminSystem::int($_POST['user']) : $promo['user'];
    $aData['server'] = isset($_POST['server']) ? AdminSystem::int($_POST['server']) : $promo['server'];
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : date('d/m/Y H:i', $promo['time']);
    $aData['data'] = isset($_POST['data']) ? trim($_POST['data']) : $promo['data'];

    $aData['time'] = AdminSystem::checkdate($aData['time']);

    if (AdminSystem::valid($aData['cod'], 'promo')) {
        AdminSystem::outjs(['e' => 'Неправильный формат промо-кода']);
    }

    $sql->query('SELECT `id` FROM `promo` WHERE `id`!="' . $id . '" AND `cod`="' . $aData['cod'] . '" AND `tarif`="' . $promo['tarif'] . '" LIMIT 1');
    if ($sql->num()) {
        AdminSystem::outjs(['e' => 'Указанный код используется в другой акции']);
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

    if ($aData['discount']) {
        $proc = strpos($aData['value'], '%') ? '%' : '';
    }

    $aData['value'] = AdminSystem::int($aData['value']) . $proc;

    $sql->query('UPDATE `promo` set '
        . '`cod`="' . $aData['cod'] . '",'
        . '`value`="' . $aData['value'] . '",'
        . '`discount`="' . $aData['discount'] . '",'
        . '`data`="' . base64_encode('{' . $aData['data'] . '}') . '",'
        . '`hits`="' . $aData['hits'] . '",'
        . '`use`="' . $aData['use'] . '",'
        . '`extend`="' . $aData['extend'] . '",'
        . '`user`="' . $aData['user'] . '",'
        . '`server`="' . $aData['server'] . '",'
        . '`time`="' . $aData['time'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    AdminSystem::outjs(['s' => 'ok']);
}

$sql->query('SELECT `id`, `unit`, `name`, `game` FROM `tarifs` WHERE `id`="' . $promo['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$sql->query('SELECT `id`, `name` FROM `units` WHERE `id`="' . $tarif['unit'] . '" LIMIT 1');
$unit = $sql->get();

$html->get('promo', 'sections/promo');

$html->set('id', $promo['id']);
$html->set('cod', $promo['cod']);
$html->set('value', $promo['value']);
$html->set('data', str_replace(['{', '}'], '', base64_decode($promo['data'])));
$html->set('hits', $promo['hits']);
$html->set('use', $promo['use']);
$html->set('user', $promo['user']);
$html->set('server', $promo['server']);
$html->set('time', date('d/m/Y H:i', $promo['time']));

$html->set('discount', $promo['discount'] ? '<option value="1">Скидка</option><option value="0">Подарочные дни</option>' : '<option value="0">Подарочные дни</option><option value="1">Скидка</option>');
$html->set('extend', $promo['extend'] ? '<option value="1">Для продления</option><option value="0">Для аренды</option>' : '<option value="0">Для аренды</option><option value="1">Для продления</option>');

$html->set('tarif', $unit['name'] . ' / #' . $tarif['id'] . ' ' . $tarif['name'] . ' (' . strtoupper($tarif['game']) . ')');

$html->pack('main');
