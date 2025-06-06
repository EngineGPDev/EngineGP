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

$aData = [];

$aData['login'] = isset($_POST['login']) ? trim($_POST['login']) : $us['login'];
$aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : '';
$aData['mail'] = isset($_POST['mail']) ? trim($_POST['mail']) : $us['mail'];
$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $us['name'];
$aData['lastname'] = isset($_POST['lastname']) ? trim($_POST['lastname']) : $us['lastname'];
$aData['patronymic'] = isset($_POST['patronymic']) ? trim($_POST['patronymic']) : $us['patronymic'];
$aData['contacts'] = isset($_POST['contacts']) ? trim($_POST['contacts']) : $us['contacts'];
$aData['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : $us['phone'];
$aData['confirm_phone'] = isset($_POST['confirm_phone']) ? trim($_POST['confirm_phone']) : $us['confirm_phone'];
$aData['group'] = isset($_POST['group']) ? trim($_POST['group']) : $us['group'];
$aData['balance'] = isset($_POST['balance']) ? trim($_POST['balance']) : $us['balance'];
$aData['help'] = isset($_POST['help']) ? trim($_POST['help']) : $us['help'];
$aData['part_money'] = isset($_POST['part_money']) ? trim($_POST['part_money']) : $us['part_money'];
$aData['support_info'] = isset($_POST['support_info']) ? trim($_POST['support_info']) : $us['support_info'];
$aData['level'] = isset($_POST['level']) ? trim($_POST['level']) : $us['level'];
$aData['replenish'] = isset($_POST['replenish']) ? trim($_POST['replenish']) : 0;
$aData['rental'] = isset($_POST['rental']) ? trim($_POST['rental']) : 0;
$aData['extend'] = isset($_POST['extend']) ? trim($_POST['extend']) : 0;

$arr_other = [
    'login' => 'логина',
    'mail' => 'почты',
];

$arr_other_null = [
    'name' => 'имени',
    'lastname' => 'фамилии',
    'patronymic' => 'отчества',
    'contacts' => 'контактов',
    'phone' => 'номера',
];

foreach ($arr_other as $input => $name) {
    if (AdminSystem::valid($aData[$input], 'other', $aValid[$input])) {
        AdminSystem::outjs(['e' => 'Неправильный формат ' . $name]);
    }
}

foreach ($arr_other as $input => $name) {
    if ($aData[$input] == '') {
        continue;
    }

    if (AdminSystem::valid($aData[$input], 'other', $aValid[$input])) {
        AdminSystem::outjs(['e' => 'Неправильный формат ' . $name]);
    }
}

$sql->query('SELECT `id` FROM `users` WHERE `id`!="' . $id . '" AND `login`="' . $aData['login'] . '" LIMIT 1');
if ($sql->num()) {
    AdminSystem::outjs(['e' => 'Логин занят другим пользователем']);
}

$sql->query('SELECT `id` FROM `users` WHERE `id`!="' . $id . '" AND `mail`="' . $aData['mail'] . '" LIMIT 1');
if ($sql->num()) {
    AdminSystem::outjs(['e' => 'Почта занята другим пользователем']);
}

if ($aData['contacts'] != '') {
    $sql->query('SELECT `id` FROM `users` WHERE `id`!="' . $id . '" AND `contacts`="' . $aData['contacts'] . '" LIMIT 1');
    if ($sql->num()) {
        AdminSystem::outjs(['e' => 'Контакты заняты другим пользователем']);
    }
}

if ($aData['phone'] != '') {
    $sql->query('SELECT `id` FROM `users` WHERE `id`!="' . $id . '" AND `phone`="' . $aData['phone'] . '" LIMIT 1');
    if ($sql->num()) {
        AdminSystem::outjs(['e' => 'Номер занят другим пользователем']);
    }
}

if ($aData['passwd'] != '') {
    if (AdminSystem::valid($aData['passwd'], 'other', $aValid['passwd'])) {
        AdminSystem::outjs(['e' => 'Неправильный формат пароля']);
    }

    $aData['passwd'] = AdminSystem::passwdkey($aData['passwd']);
} else {
    $aData['passwd'] = $us['passwd'];
}

$aData['help'] = $aData['help'] == 0 ? 0 : 1;
$aData['confirm_phone'] = $aData['confirm_phone'] == 0 ? 0 : 1;
$aData['group'] = in_array($aData['group'], ['user', 'support', 'admin']) ? $aData['group'] : $us['group'];
$aData['level'] = in_array($aData['level'], [0, 1, 2]) ? $aData['level'] : $us['level'];

if ($aData['support_info'] != '' and AdminSystem::valid($aData['support_info'], 'other', $aValid['support_info'])) {
    AdminSystem::outjs(['e' => 'Неправильный формат подписи']);
}

if ($aData['balance'] == '') {
    $aData['balance'] = 0;
}

if (!is_numeric($aData['balance'])) {
    AdminSystem::outjs(['e' => 'Неправильный формат баланса']);
}

if (!is_numeric($aData['part_money'])) {
    AdminSystem::outjs(['e' => 'Неправильный формат заработанных средств']);
}

if ($aData['replenish'] > 0) {
    if (!is_numeric($aData['replenish'])) {
        AdminSystem::outjs(['e' => 'Неправильный формат суммы пополнения']);
    }

    $aData['balance'] += $aData['replenish'];

    $sql->query('INSERT INTO `logs` set `user`="' . $id . '", `text`="Пополнение баланса на сумму: ' . $aData['replenish'] . ' ' . $cfg['currency'] . ' (прямой платеж)", `date`="' . $start_point . '", `type`="replenish", `money`="' . $aData['replenish'] . '"');
}

if ($aData['part_money'] < $us['part_money']) {
    $cashout = round($us['part_money'] - $aData['part_money'], 2);

    $sql->query('INSERT INTO `logs` set `user`="' . $id . '", `text`="Вывод заработанных средств: ' . $cashout . ' ' . $cfg['currency'] . '", `date`="' . $start_point . '", `type`="cashout", `money`="' . $cashout . '"');
}

if ($aData['rental']) {
    $rental = AdminSystem::int($aData['rental']);

    if ($rental) {
        $aData['rental'] = strpos($aData['rental'], '%') ? $rental . '%' : $rental;
    } else {
        $aData['rental'] = 0;
    }
} else {
    $aData['rental'] = 0;
}

if (strlen($aData['rental']) > 4) {
    AdminSystem::outjs(['e' => 'Неправильно указана скидка на аренду']);
}

if ($aData['extend']) {
    $extend = AdminSystem::int($aData['extend']);

    if ($extend) {
        $aData['extend'] = strpos($aData['extend'], '%') ? $extend . '%' : $extend;
    } else {
        $aData['extend'] = 0;
    }
} else {
    $aData['extend'] = 0;
}

if (strlen($aData['extend']) > 4) {
    AdminSystem::outjs(['e' => 'Неправильно указана скидка на аренду']);
}

$sql->query('UPDATE `users` set '
    . '`login`="' . $aData['login'] . '",'
    . '`mail`="' . $aData['mail'] . '",'
    . '`passwd`="' . $aData['passwd'] . '",'
    . '`name`="' . $aData['name'] . '",'
    . '`lastname`="' . $aData['lastname'] . '",'
    . '`patronymic`="' . $aData['patronymic'] . '",'
    . '`balance`="' . $aData['balance'] . '",'
    . '`part_money`="' . $aData['part_money'] . '",'
    . '`group`="' . $aData['group'] . '",'
    . '`support_info`="' . $aData['support_info'] . '",'
    . '`level`="' . $aData['level'] . '",'
    . '`contacts`="' . $aData['contacts'] . '",'
    . '`phone`="' . $aData['phone'] . '",'
    . '`confirm_phone`="' . $aData['confirm_phone'] . '",'
    . '`help`="' . $aData['help'] . '",'
    . '`rental`="' . $aData['rental'] . '",'
    . '`extend`="' . $aData['extend'] . '" WHERE `id`="' . $id . '" LIMIT 1');

AdminSystem::outjs(['s' => 'ok']);
