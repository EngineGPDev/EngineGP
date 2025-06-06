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
use EngineGP\Model\Parameters;
use EngineGP\Infrastructure\GeoIP\SxGeo;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$status = [
    'working' => '<span class="text-green">Работает</span>',
    'off' => '<span class="text-red">Выключен</span>',
    'start' => 'Запускается',
    'restart' => 'Перезапускается',
    'change' => 'Смена карты',
    'install' => 'Устанавливается',
    'reinstall' => 'Переустанавливается',
    'update' => 'Обновляется',
    'recovery' => 'Восстанавливается',
    'overdue' => 'Просрочен',
    'blocked' => 'Заблокирован',
];

$sql->query('SELECT * FROM `users` WHERE `id`="' . $id . '" LIMIT 1');
$us = $sql->get();

if ($go) {
    include(SEC . 'users/action.php');
}

$auth_list = '';

$SxGeo = new SxGeo(DATA . 'SxGeoCity.dat');

$sql->query('SELECT `ip`, `date`, `browser` FROM `auth` WHERE `user`="' . $id . '" ORDER BY `id` DESC LIMIT 15');
while ($auth_info = $sql->get()) {
    $auth_list .= '<tr>';
    $auth_list .= '<td>Авторизация через браузер: <b>' . AdminSystem::browser(base64_decode($auth_info['browser'])) . '</b></td>';
    $auth_list .= '<td>' . $auth_info['ip'] . '</td>';
    $auth_list .= '<td>' . AdminSystem::country($auth_info['ip']) . '</td>';
    $auth_list .= '<td>' . AdminSystem::today($auth_info['date']) . '</td>';
    $auth_list .= '</tr>';
}

$logs_list = '';

$sql->query('SELECT `text`, `date` FROM `logs` WHERE `user`="' . $id . '" ORDER BY `id` DESC LIMIT 30');
while ($logs_info = $sql->get()) {
    $logs_list .= '<tr>';
    $logs_list .= '<td>' . $logs_info['text'] . '</td>';
    $logs_list .= '<td>' . AdminSystem::today($logs_info['date']) . '</td>';
    $logs_list .= '</tr>';
}

$serv_user = '';

$servers_user = $sql->query('SELECT `id`, `unit`, `tarif`, `user`, `address`, `game`, `status`, `slots`, `name`, `time`, `online` FROM `servers` WHERE `user`="' . $id . '" ORDER BY `id` DESC LIMIT 30');
while ($server_user = $sql->get($servers_user)) {
    $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server_user['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $sql->query('SELECT `name` FROM `tarifs` WHERE `id`="' . $server_user['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    $serv_user .= '<tr>';
    $serv_user .= '<td>' . $server_user['id'] . '</td>';
    $serv_user .= '<td>' . $server_user['address'] . ' <a href="[home]servers/id/' . $server_user['id'] . '">(Перейти)</a></td>';
    $serv_user .= '<td>#' . $server_user['tarif'] . ' ' . $tarif['name'] . '</td>';
    $serv_user .= '<td>' . $status[$server_user['status']] . '</td>';
    $serv_user .= '<td>' . strtoupper($aGname[$server_user['game']]) . '</td>';
    $serv_user .= '<td>' . $time_end = $server_user['status'] == 'overdue' ? 'Удаление через: ' . AdminSystem::date('min', $server_user['overdue'] + $cfg['server_delete'] * 86400) : 'Осталось: ' . AdminSystem::date('min', $server_user['time']) . '</td>';
    $serv_user .= '</tr>';
}

$money_all = 0;
$money_buy = 0;
$money_month = 0;

$sql->query('SELECT `money` FROM `logs` WHERE `user`="' . $us['id'] . '" AND (`type`="buy" OR `type`="extend")');
while ($logs = $sql->get()) {
    $money_all += $logs['money'];
}

$sql->query('SELECT `money` FROM `logs` WHERE `user`="' . $us['id'] . '" AND `type`="buy"');
while ($logs = $sql->get()) {
    $money_buy += $logs['money'];
}

$time = (Parameters::$aDayMonth[date('n', $start_point)] - date('j', $start_point)) * 86400;

$sql->query('SELECT `money` FROM `logs` WHERE `user`="' . $us['id'] . '" AND (`type`="buy" OR `type`="extend") AND `date`>"' . ($start_point - $time) . '"');
while ($logs = $sql->get()) {
    $money_month += $logs['money'];
}

$html->get('user', 'sections/users');

foreach ($us as $i => $val) {
    $html->set($i, $val);
}

$html->set('time', $us['time'] < $start_point - 600 ? AdminSystem::today($us['time']) : AdminSystem::ago($us['time']));
$html->set('date', AdminSystem::today($us['date']));

$html->set('month', mb_strtolower(Parameters::$aNameMonth[AdminSystem::int(date('n', $start_point))], 'UTF-8'));
$html->set('money_all', $money_all);
$html->set('money_buy', $money_buy);
$html->set('money_extend', $money_all - $money_buy);
$html->set('money_month', $money_month);

$html->set('auth', $auth_list);
$html->set('logs', $logs_list);
$html->set('serv_user', $serv_user);

$html->set('confirm_phone', str_replace($us['confirm_phone'], $us['confirm_phone'] . '" selected="select', '<option value="0">Не подтвержден</option><option value="1">Подтвержден</option>'));
$html->set('group', str_replace($us['group'], $us['group'] . '" selected="select', '<option value="support">Тех. поддержка</option><option value="user">Пользователь</option><option value="admin">Администратор</option>'));
$html->set('help', str_replace($us['help'], $us['help'] . '" selected="select', '<option value="0">Доступ к разделу "тех.поддержка" разрешен</option><option value="1">Доступ к разделу "тех.поддержка" запрещен</option>'));

if ($us['group'] == 'support') {
    $html->set('level', str_replace($us['level'] . '"', $us['level'] . '" selected="select"', '<option value="0">Уровень 0</option><option value="1">Уровень 1</option><option value="2">Уровень 2</option>'));
    $html->unit('support', true, true);
} else {
    $html->unit('support', false, true);
}

$html->set('rental', $us['rental']);
$html->set('extend', $us['extend']);

$html->pack('main');
