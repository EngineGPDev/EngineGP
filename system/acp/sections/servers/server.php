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

$sql->query('SELECT `time`, `overdue` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

if ($server['time'] > $start_point and $server['overdue']) {
    $sql->query('UPDATE `servers` set `overdue`="0" WHERE `id`="' . $id . '" LIMIT 1');
}

$sql->query('SELECT * FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = $sql->get();

$sql->query('SELECT `address` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `name`, `slots_min`, `slots_max`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$aData = [];

if ($go) {
    if (isset($url['type']) and in_array($url['type'], ['overdue', 'block', 'tarif'])) {
        if ($url['type'] != 'tarif') {
            $time = isset($_POST['time']) ? trim($_POST['time']) : sys::outjs(['e' => 'Необходимо указать дату.']);

            $date = sys::checkdate($time);
        }

        switch ($url['type']) {
            case 'overdue':
                if ($server['time'] > $start_point) {
                    sys::outjs(['e' => 'Игровой сервер должен быть просрочен.']);
                }

                $sql->query('UPDATE `servers` set `overdue`="' . $date . '" WHERE `id`="' . $id . '" LIMIT 1');
                break;

            case 'block':
                if ($server['status'] != ('off' || 'overdue')) {
                    sys::outjs(['e' => 'Игровой сервер должен быть выключен.']);
                }

                if ($date < $start_point) {
                    $sql->query('UPDATE `servers` set `status`="off", `block`="0" WHERE `id`="' . $id . '" LIMIT 1');
                } else {
                    $sql->query('UPDATE `servers` set `status`="blocked", `block`="' . $date . '" WHERE `id`="' . $id . '" LIMIT 1');
                }
                break;

            case 'tarif':
                $tid = isset($url['tarif']) ? sys::int($url['tarif']) : sys::outjs(['e' => 'Необходимо указать тариф.']);

                if ($tid == $server['tarif']) {
                    sys::outjs(['s' => 'ok']);
                }

                $sql->query('SELECT `id`, `slots_min`, `slots_max`, `packs`, `fps`, `tickrate`, `ram` FROM `tarifs` WHERE `id`="' . $tid . '" AND `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" LIMIT 1');
                if (!$sql->num()) {
                    sys::outjs(['e' => 'Укажите тариф из списка.']);
                }

                $tarif = $sql->get();

                if ($server['slots'] < $tarif['slots_min'] || $server['slots'] > $tarif['slots_max']) {
                    sys::outjs(['e' => 'Данный тариф не совместим по слотам.']);
                }

                if ($server['fps']) {
                    if (!in_array($server['fps'], explode(':', $tarif['fps']))) {
                        sys::outjs(['e' => 'Данный тариф не совместим по FPS.']);
                    }
                }

                if ($server['tickrate']) {
                    if (!in_array($server['tickrate'], explode(':', $tarif['tickrate']))) {
                        sys::outjs(['e' => 'Данный тариф не совместим по TickRate.']);
                    }
                }

                if ($server['game'] == 'mc') {
                    $ram = $server['ram'] / $server['slots'];

                    if (!in_array($ram, explode(':', $tarif['ram']))) {
                        sys::outjs(['e' => 'Данный тариф не совместим по RAM.']);
                    }
                }

                if (!array_key_exists($server['pack'], sys::b64djs($tarif['packs']))) {
                    sys::outjs(['e' => 'На данном тарифном плане нет сборки игрового сервера.']);
                }

                $sql->query('UPDATE `servers` set `tarif`="' . $tid . '" WHERE `id`="' . $id . '" LIMIT 1');
                break;
        }

        sys::outjs(['s' => 'ok']);
    }

    $aData['user'] = isset($_POST['user']) ? sys::int($_POST['user']) : $server['user'];
    $aData['address'] = isset($_POST['address']) ? trim($_POST['address']) : $server['address'];
    $aData['port'] = isset($_POST['port']) ? sys::int($_POST['port']) : $server['port'];
    $aData['hdd'] = isset($_POST['hdd']) ? sys::int($_POST['hdd']) : $server['hdd'];
    $aData['slots'] = isset($_POST['slots']) ? trim($_POST['slots']) : $server['slots'];
    $aData['pack'] = isset($_POST['pack']) ? trim($_POST['pack']) : $server['pack'];
    $aData['fps'] = isset($_POST['fps']) ? sys::int($_POST['fps']) : $server['fps'];
    $aData['tickrate'] = isset($_POST['tickrate']) ? sys::int($_POST['tickrate']) : $server['tickrate'];
    $aData['ram'] = isset($_POST['ram']) ? sys::int($_POST['ram']) : $server['ram'];
    $aData['cpu'] = isset($_POST['cpu']) ? sys::int($_POST['cpu']) : $server['cpu'];
    $aData['pingboost'] = isset($_POST['pingboost']) ? sys::int($_POST['pingboost']) : $server['pingboost'];
    $aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : $server['time'];
    $aData['ftp_use'] = $_POST['ftp_use'] ?? $server['ftp_use'];
    $aData['ftp_root'] = isset($_POST['ftp_root']) ? sys::int($_POST['ftp_root']) : $server['ftp_root'];
    $aData['plugins_use'] = $_POST['plugins_use'] ?? $server['plugins_use'];
    $aData['console_use'] = $_POST['console_use'] ?? $server['console_use'];
    $aData['stats_use'] = $_POST['stats_use'] ?? $server['stats_use'];
    $aData['copy_use'] = $_POST['copy_use'] ?? $server['copy_use'];
    $aData['web_use'] = $_POST['web_use'] ?? $server['web_use'];

    if ($server['user'] != $aData['user']) {
        $sql->query('SELECT `id` FROM `users` WHERE `id`="' . $aData['user'] . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['e' => 'Пользователь не найден.']);
        } else {
            $sql->query('SELECT `id` FROM `web` WHERE `user`!="' . $aData['user'] . '" AND `server`="' . $id . '" LIMIT 1');
            if ($sql->num()) {
                sys::outjs(['e' => 'Невозможно установить пользователя владельцем данного сервера<br>Из-за возможной несовместимости с бесплатными услугами.<br>Удалите у данного сервера бесплатные услуги.']);
            }
        }
    }

    if (sys::valid($aData['address'], 'other', $aValid['address'])) {
        $aData['address'] = $server['address'];
    }

    $sql->query('SELECT `id` FROM `servers` WHERE `id`!="' . $id . '" AND `address` LIKE \'%' . sys::first(explode(':', $unit['address'])) . '\' AND `port`="' . $aData['port'] . '" LIMIT 1');
    if ($sql->num()) {
        sys::outjs(['e' => 'Данный порт занят другим сервером.']);
    }

    $slots = explode(':', $aData['slots']);

    if (!isset($slots[0]) and !isset($slots[1])) {
        sys::outjs(['e' => 'Слоты указаны не правильно.']);
    }

    if ($slots[0] < 2 || $slots[1] < 2) {
        sys::outjs(['e' => 'Слоты указаны не правильно.']);
    }

    if ($slots[0] > $tarif['slots_max'] || $slots[0] < $tarif['slots_min']) {
        sys::outjs(['e' => 'Слоты указаны не правильно.']);
    }

    $slots[1] = $slots[1] > $slots[0] ? $slots[0] : $slots[1];

    $aPacks = sys::b64djs($tarif['packs']);

    if (!array_key_exists($aData['pack'], $aPacks)) {
        sys::outjs(['e' => 'Указанная сборка не найдена.']);
    }

    if (!in_array($aData['pingboost'], [1, 2, 3])) {
        $aData['pingboost'] = 0;
    }

    $aData['time'] = sys::checkdate($aData['time']);

    foreach (['ftp_use', 'plugins_use', 'console_use', 'stats_use', 'copy_use', 'web_use'] as $section) {
        $aData[$section] = (string)$aData[$section] == 'on' ? '1' : '0';
    }

    $sql->query('UPDATE `servers` set '
        . '`user`="' . $aData['user'] . '",'
        . '`address`="' . $aData['address'] . '",'
        . '`port`="' . $aData['port'] . '",'
        . '`slots`="' . $slots[0] . '",'
        . '`slots_start`="' . $slots[1] . '",'
        . '`fps`="' . $aData['fps'] . '",'
        . '`tickrate`="' . $aData['tickrate'] . '",'
        . '`ram`="' . $aData['ram'] . '",'
        . '`cpu`="' . $aData['cpu'] . '",'
        . '`pingboost`="' . $aData['pingboost'] . '",'
        . '`time`="' . $aData['time'] . '",'
        . '`ftp_use`="' . $aData['ftp_use'] . '",'
        . '`ftp_root`="' . $aData['ftp_root'] . '",'
        . '`plugins_use`="' . $aData['plugins_use'] . '",'
        . '`console_use`="' . $aData['console_use'] . '",'
        . '`stats_use`="' . $aData['stats_use'] . '",'
        . '`copy_use`="' . $aData['copy_use'] . '",'
        . '`web_use`="' . $aData['web_use'] . '",'
        . '`pack`="' . $aData['pack'] . '",'
        . '`hdd`="' . $aData['hdd'] . '" WHERE `id`="' . $id . '" LIMIT 1');

    $sql->query('UPDATE `web` set `user`="' . $aData['user'] . '" WHERE `server`="' . $id . '"');

    $mcache->delete('server_index_' . $id);
    $mcache->delete('server_resources_' . $id);
    $mcache->delete('server_status_' . $id);

    sys::outjs(['s' => 'ok']);
}

$sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$packs = '';

$aPacks = sys::b64djs($tarif['packs']);

foreach ($aPacks as $name => $fullname) {
    $packs .= '<option value="' . $name . '">' . $fullname . '</option>';
}

$packs = str_replace('"' . $server['pack'] . '"', '"' . $server['pack'] . '" selected', $packs);

$pingboost = str_replace('="' . $server['pingboost'] . '"', '="' . $server['pingboost'] . '" selected', '<option value="0">По умолчанию</option><option value="1">PINGBOOST 1</option><option value="2">PINGBOOST 2</option><option value="3">PINGBOOST 3</option>');
$ftp_root = $server['ftp_root'] ? '<option value="1">Корневой каталог</option><option value="0">Обычный (cstrike)</option>' : '<option value="0">Обычный (cstrike)</option><option value="1">Корневой каталог</option>';

$tarifs = '';

$sql->query('SELECT `id`, `name` FROM `tarifs` WHERE `unit`="' . $server['unit'] . '" AND `game`="' . $server['game'] . '" AND `id`!="' . $server['tarif'] . '"');
while ($tarif_list = $sql->get()) {
    $tarifs .= '<option value="' . $tarif_list['id'] . '">' . $tarif_list['name'] . '</option>';
}

$copys = $sql->query('SELECT `id`, `user` FROM `copy` WHERE `server`="' . $id . '" LIMIT 10');
while ($copy = $sql->get($copys)) {
    $aCP = explode('_', $copy['user']);

    if (isset($aCP[0]) && isset($aData['user']) && $aCP[0] != $aData['user']) {
        $sql->query('UPDATE `copy` set `user`="' . $aData['user'] . '_' . $aCP[1] . '" WHERE `id`="' . $copy['id'] . ' LIMIT 1');
    }
}

$html->get('server', 'sections/servers');
$html->set('id', $id);
$html->set('name', $server['name']);
$html->set('address', $server['address']);
$html->set('port', $server['port']);
$html->set('slots', $server['slots']);
$html->set('slots_start', $server['slots_start']);
$html->set('user', $server['user']);
$html->set('game', $server['game']);
$html->set('unit', $unit['name']);
$html->set('tarif', '#' . $server['tarif'] . ' ' . $tarif['name']);
$html->set('hdd', $server['hdd']);
$html->set('fps', $server['fps']);
$html->set('tickrate', $server['tickrate']);
$html->set('ram', $server['ram']);
$html->set('cpu', $server['cpu']);
$html->set('ftp_on', $server['ftp_on'] ? 'Использовался' : 'Не использовался');
$html->set('tarifs', $tarifs);
$html->set('pingboost', $pingboost);
$html->set('ftp_root', $ftp_root);
$html->set('packs', $packs);
$html->set('time', date('d/m/Y H:i', $server['time']));
$html->set('date', date('d.m.Y - H:i:s', $server['date']));
$html->set('overdue', $server['overdue'] == 0 ? 'Установить' : date('d/m/Y H:i', $server['overdue']));
$html->set('block', $server['block'] == 0 ? 'Заблокировать' : date('d/m/Y H:i', $server['block']));

foreach (['ftp_use', 'plugins_use', 'console_use', 'stats_use', 'copy_use', 'web_use'] as $section) {
    if ($server[$section]) {
        $html->unit($section, 1);
    } else {
        $html->unit($section);
    }
}

$html->pack('main');
