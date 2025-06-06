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

$sql->query('SELECT `name`, `lastname`, `patronymic`, `mail`, `wmr`, `phone`, `contacts`, `date`, `part_money`, `rental`, `extend` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
$user = array_merge($user, $sql->get());

// Подсчет рефералов
$nmch = 'part_' . $user['id'];

if ($mcache->get($nmch) != '') {
    $part_users = $mcache->get($nmch);
} else {
    $sql->query('SELECT `id` FROM `users` WHERE `part`="' . $user['id'] . '"');
    $part_users = $sql->num();

    $mcache->set($nmch, $part_users, false, 300);
}

if ($user['rental']) {
    $rental = strpos($user['rental'], '%') ? $user['rental'] : $user['rental'] . ' ' . $cfg['currency'];
} else {
    $rental = 'отсутствует';
}

if ($user['extend']) {
    $extend = strpos($user['extend'], '%') ? $user['extend'] : $user['extend'] . ' ' . $cfg['currency'];
} else {
    $extend = 'отсутствует';
}

$i = 1;

$part_user = '';

$part_inf = $sql->query('SELECT `id`, `login`, `date` FROM `users` WHERE `part`="' . $user['id'] . '" ORDER BY `date` ASC');
while ($part_info = $sql->get($part_inf)) {
    $sql->query('SELECT `id` FROM `servers` WHERE `user`="' . $part_info['id'] . '" LIMIT 10');
    $servers = $sql->num();

    $part_user .= '<tr>';
    $part_user .= '<td>' . $i++ . '</td>';
    $part_user .= '<td>' . $part_info['login'] . '</td>';
    $part_user .= '<td>' . System::today($part_info['date'], true) . '</td>';
    $part_user .= '<td>' . $servers . '</td>';
    $part_user .= '</tr>';
}

$html->get('index', 'sections/user/lk');

$html->set('id', $user['id']);
$html->set('name', $user['name']);
$html->set('lastname', $user['lastname']);
$html->set('patronymic', $user['patronymic']);
$html->set('login', $user['login']);
$html->set('mail', $user['mail']);
$html->set('phone', $user['phone']);
$html->set('contacts', $user['contacts']);
$html->set('cur', $cfg['currency']);
$html->set('wmr', $user['wmr']);
$html->set('rental', $rental);
$html->set('extend', $extend);
$html->set('date', System::today($user['date'], true));
$html->set('balance', round($user['balance'], 2));

$html->set('part_users', $part_users);
$html->set('part_money', $user['part_money']);
$html->set('part_user', $part_user);

if ($user['name']) {
    $html->unit('name', true);
} else {
    $html->unit('name');
}
if ($user['lastname']) {
    $html->unit('lastname', true);
} else {
    $html->unit('lastname');
}
if ($user['patronymic']) {
    $html->unit('patronymic', true);
} else {
    $html->unit('patronymic');
}

if ($user['name'] || $user['lastname'] || $user['patronymic']) {
    $html->unit('nlp', true);
} else {
    $html->unit('nlp');
}

if (isset($user['wmr'][0]) and in_array($user['wmr'][0], ['R', 'Z', 'U'])) {
    $html->unit('wmr', true, true);
} else {
    $html->unit('wmr', false, true);
}

$html->pack('main');
