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
        sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);
    }

    $aData = [];

    $aData['active'] = isset($_POST['active']) && is_array($_POST['active']) ? $_POST['active'] : [];
    $aData['value'] = isset($_POST['value']) && is_array($_POST['value']) ? $_POST['value'] : [];
    $aData['passwd'] = isset($_POST['passwd']) && is_array($_POST['passwd']) ? $_POST['passwd'] : [];
    $aData['flags'] = isset($_POST['flags']) && is_array($_POST['flags']) ? $_POST['flags'] : [];
    $aData['type'] = isset($_POST['type']) && is_array($_POST['type']) ? $_POST['type'] : [];
    $aData['time'] = isset($_POST['time']) && is_array($_POST['time']) ? $_POST['time'] : [];
    $aData['info'] = isset($_POST['info']) && is_array($_POST['info']) ? $_POST['info'] : [];

    // Удаление текущих записей
    $sql->query('DELETE FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '"');

    $usini = '';

    foreach ($aData['value'] as $index => $val) {
        if ($val != '') {
            $type = $aData['type'][$index] ?? 'a';
            if (!in_array($type, ['c', 'ce', 'de', 'a'])) {
                $type = 'a';
            }

            $aDate = isset($aData['time'][$index]) ? explode('.', $aData['time'][$index]) : explode('.', date('d.m.Y', $start_point));

            if (!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2])) {
                $aDate = explode('.', date('d.m.Y', $start_point));
            }

            $time = mktime(0, 0, 0, $aDate[1], $aDate[0], $aDate[2]);

            $aData['active'][$index] = isset($aData['active'][$index]) ? 1 : 0;
            $aData['passwd'][$index] ??= '';
            $aData['flags'][$index] ??= '';
            $aData['info'][$index] ??= '';

            $text = '"' . $val . '" "' . $aData['passwd'][$index] . '" "' . $aData['flags'][$index] . '" "' . $type . '"';

            $sql->query('INSERT INTO `admins_' . $server['game'] . '` set'
                . '`server`="' . $id . '",'
                . '`value`="' . htmlspecialchars($val) . '",'
                . '`active`="' . $aData['active'][$index] . '",'
                . '`passwd`="' . htmlspecialchars($aData['passwd'][$index]) . '",'
                . '`flags`="' . htmlspecialchars($aData['flags'][$index]) . '",'
                . '`type`="' . $type . '",'
                . '`time`="' . $time . '",'
                . '`text`="' . htmlspecialchars($text) . '",'
                . '`info`="' . htmlspecialchars($aData['info'][$index]) . '"');

            if ($aData['active'][$index]) {
                $usini .= $text . PHP_EOL;
            }
        }
    }

    $temp = sys::temp($usini);

    $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/cstrike/addons/amxmodx/configs/users.ini');
    $ssh->set('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/cstrike/addons/amxmodx/configs/users.ini');

    unlink($temp);

    $ssh->set('chown server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/cstrike/addons/amxmodx/configs/users.ini');

    $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"amx_reloadadmins\" C-m");

    sys::outjs(['s' => 'ok'], $nmch);
}

// Построение списка добавленных админов
$sql->query('SELECT `id`, `value`, `active`, `passwd`, `flags`, `type`, `time`, `info` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '" ORDER BY `id` ASC');
while ($admin = $sql->get()) {
    switch ($admin['type']) {
        case 'c':
            $type = '<option value="c">SteamID/Пароль</option><option value="a">Ник/Пароль</option><option value="ce">SteamID</option><option value="de">IP Адрес</option>';
            break;

        case 'ce':
            $type = '<option value="ce">SteamID</option><option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="de">IP Адрес</option>';
            break;

        case 'de':
            $type = '<option value="de">IP Адрес</option><option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="ce">SteamID</option>';
            break;

        default:
            $type = '<option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="ce">SteamID</option><option value="de">IP Адрес</option>';
    }

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
    $html->set('type', $type);
    $html->set('time', date('d.m.y', $admin['time']));
    $html->set('info', $admin['info']);

    $html->pack('admins');
}

$sql->query('SELECT `id` FROM `admins_' . $server['game'] . '` WHERE `server`="' . $id . '" ORDER BY `id` DESC LIMIT 1');
$max = $sql->get();

$ip = $server['address'];
$port = $server['port'];

$html->get('admins', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('admins', $html->arr['admins'] ?? '');
$html->set('index', isset($max['id']) < 1 ? 0 : $max['id']);
$html->set('address', 'ip/' . $ip . '/port/' . $port);

$sql->query('SELECT `active` FROM `privileges` WHERE `server`="' . $id . '" LIMIT 1');
if ($sql->num()) {
    $privilege = $sql->get();

    if ($privilege['active']) {
        $html->unit('privileges', 1);
    } else {
        $html->unit('privileges');
    }
} else {
    $html->unit('privileges');
}

$html->pack('main');
