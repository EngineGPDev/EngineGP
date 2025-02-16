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

$html->nav('Управление администраторами');

if ($go) {
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    include(LIB . 'ssh.php');

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        System::outjs(['e' => System::text('error', 'ssh')], $nmch);
    }

    $aData = [];

    $aData['active'] = isset($_POST['active']) && is_array($_POST['active']) ? $_POST['active'] : [];
    $aData['value'] = isset($_POST['value']) && is_array($_POST['value']) ? $_POST['value'] : [];
    $aData['passwd'] = isset($_POST['passwd']) && is_array($_POST['passwd']) ? $_POST['passwd'] : [];
    $aData['flags'] = isset($_POST['flags']) && is_array($_POST['flags']) ? $_POST['flags'] : [];
    $aData['immunity'] = isset($_POST['immunity']) && is_array($_POST['immunity']) ? System::int($_POST['immunity']) : [];
    $aData['time'] = isset($_POST['time']) && is_array($_POST['time']) ? $_POST['time'] : [];
    $aData['info'] = isset($_POST['info']) && is_array($_POST['info']) ? $_POST['info'] : [];

    // Удаление текущих записей
    $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '"');

    $usini = '';

    foreach ($aData['value'] as $index => $val) {
        if ($val != '') {
            $aDate = isset($aData['time'][$index]) ? explode('.', $aData['time'][$index]) : explode('.', date('d.m.Y', $start_point));

            if (!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2])) {
                $aDate = explode('.', date('d.m.Y', $start_point));
            }

            $time = mktime(0, 0, 0, $aDate[1], $aDate[0], $aDate[2]);

            $aData['active'][$index] = isset($aData['active'][$index]) ? 1 : 0;
            $aData['passwd'][$index] ??= '';
            $aData['flags'][$index] ??= '';
            $aData['info'][$index] ??= '';

            $text = '"' . $val . '" "' . $aData['immunity'][$index] . ':' . $aData['flags'][$index] . '" "' . $aData['passwd'][$index] . '"';

            $sql->query('INSERT INTO `admins_' . $server['game'] . '` set'
                . '`server`="' . $id . '",'
                . '`value`="' . htmlspecialchars($val) . '",'
                . '`active`="' . $aData['active'][$index] . '",'
                . '`passwd`="' . htmlspecialchars($aData['passwd'][$index]) . '",'
                . '`flags`="' . htmlspecialchars($aData['flags'][$index]) . '",'
                . '`immunity`="' . $aData['immunity'][$index] . '",'
                . '`time`="' . $time . '",'
                . '`text`="' . htmlspecialchars($text) . '",'
                . '`info`="' . htmlspecialchars($aData['info'][$index]) . '"');

            if ($aData['active'][$index]) {
                $usini .= $text . PHP_EOL;
            }
        }
    }

    $temp = System::temp($usini);

    $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/cstrike/addons/sourcemod/configs/admins_simple.ini');
    $ssh->set('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/cstrike/addons/sourcemod/configs/admins_simple.ini');

    unlink($temp);

    $ssh->set('chown server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/cstrike/addons/sourcemod/configs/admins_simple.ini');

    $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \" sm_reloadadmins\" C-m");

    System::outjs(['s' => 'ok'], $nmch);
}

// Построение списка добавленных админов
$sql->query('SELECT `id`, `value`, `active`, `passwd`, `flags`, `immunity`, `time`, `info` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '" ORDER BY `id` ASC');
while ($admin = $sql->get()) {
    $html->get('list', 'sections/servers/' . $server['game'] . '/settings/admins');

    if ($admin['active']) {
        $html->unit('active', 1);
    } else {
        $html->unit('active');
    }

    $html->set('id', $admin['id']);
    $html->set('value', $admin['value']);
    $html->set('passwd', $admin['passwd']);
    $html->set('flags', $admin['flags']);
    $html->set('immunity', $admin['immunity']);
    $html->set('time', date('d.m.y', $admin['time']));
    $html->set('info', $admin['info']);

    $html->pack('admins');
}

$sql->query('SELECT `id` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '" ORDER BY `id` DESC LIMIT 1');
$max = $sql->get();

$html->get('admins', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('admins', $html->arr['admins'] ?? '');
$html->set('index', isset($max['id']) < 1 ? 0 : $max['id']);

$html->pack('main');
