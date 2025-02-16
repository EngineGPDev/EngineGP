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

// Проверка прав
if (isset($url['rights']) && $url['rights']) {
    $sql->query('SELECT `rights` FROM `owners` WHERE `id`="' . System::int($url['rights']) . '" AND `server`="' . $id . '" LIMIT 1');

    if (!$sql->num()) {
        System::outjs(['e' => 'Совладелец не найден.']);
    }

    $owner = $sql->get();

    $aRights = System::b64djs($owner['rights']);

    $rights = '';

    foreach ($aOwnersI as $access => $info) {
        if ($aRights[$access]) {
            $rights .= $info . ', ';
        }
    }

    System::outjs(['s' => substr($rights, 0, -2)]);
}

// Удаление совладельца
if (isset($url['delete']) && $url['delete']) {
    $sql->query('SELECT `rights` FROM `owners` WHERE `id`="' . System::int($url['delete']) . '" AND `server`="' . $id . '" LIMIT 1');

    if ($sql->num()) {
        $sql->query('DELETE FROM `owners` WHERE `id`="' . System::int($url['delete']) . '" AND `server`="' . $id . '" LIMIT 1');
    }

    System::back($cfg['http'] . 'servers/id/' . $id . '/section/owners');
}

// Добавление совладельца
if ($go) {
    $nmch = System::rep_act('server_owners_go_' . $id, 5);

    $aData = (isset($_POST['owner']) && is_array($_POST['owner'])) ? $_POST['owner'] : [];

    $aDate = isset($aData['\'time\'']) ? explode('.', $aData['\'time\'']) : explode('.', date('d.m.Y', $start_point));
    $aTime = explode(':', date('H:i:s', $start_point));

    if (!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2])) {
        System::outjs(['e' => 'Дата доступа указана неверно.'], $nmch);
    }

    $time = mktime($aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2]) + 3600;

    if ($time < $start_point) {
        System::outjs(['e' => 'Время доступа не может быть меньше 60 минут.'], $nmch);
    }

    // Проверка пользователя
    if (!isset($aData['\'user\''])) {
        System::outjs(['e' => 'Необходимо указать пользователя.'], $nmch);
    }

    if (is_numeric($aData['\'user\''])) {
        $sql->query('SELECT `id` FROM `users` WHERE `id`="' . $aData['\'user\''] . '" LIMIT 1');
    } else {
        if (System::valid($aData['\'user\''], 'other', $aValid['login'])) {
            System::outjs(['e' => System::text('input', 'login_valid')], $nmch);
        }

        $sql->query('SELECT `id` FROM `users` WHERE `login`="' . $aData['\'user\''] . '" LIMIT 1');
    }

    if (!$sql->num()) {
        System::outjs(['e' => 'Пользователь не найден в базе.'], $nmch);
    }

    $uowner = $sql->get();

    if ($server['user'] == $uowner['id']) {
        System::outjs(['e' => 'Владельца сервера нельзя добавить в совладельцы.'], $nmch);
    }

    $owner = $sql->query('SELECT `id` FROM `owners` WHERE `server`="' . $id . '" AND `user`="' . $uowner['id'] . '" LIMIT 1');

    $upd = $sql->num($owner);

    // Если не обновление доступа совладельца, проверить кол-во
    if (!$upd) {
        $sql->query('SELECT `id` FROM `owners` WHERE `server`="' . $id . '" AND `time`>"' . $start_point . '" LIMIT 5');

        if ($sql->num() == 5) {
            System::outjs(['e' => 'Вы добавили максимально кол-во совладельцев.'], $nmch);
        }
    }

    $aRights = [];

    $check = 0;

    foreach ($aOwners[$server['game']] as $access) {
        $aRights[$access] = isset($aData['\'' . $access . '\'']) ? 1 : 0;

        $check += $aRights[$access];
    }

    if (!$check) {
        System::outjs(['e' => 'Необходимо включить минимум одно разрешение.'], $nmch);
    }

    if ($upd) {
        $sql->query('UPDATE `owners` set `rights`="' . System::b64js($aRights) . '", `time`="' . $time . '" WHERE `server`="' . $id . '" AND `user`="' . $uowner['id'] . '" LIMIT 1');
    } else {
        $sql->query('INSERT INTO `owners` set `server`="' . $id . '", `user`="' . $uowner['id'] . '", `rights`="' . System::b64js($aRights) . '", `time`="' . $time . '"');
    }

    $sql->query('DELETE FROM `owners` WHERE `server`="' . $id . '" AND `time`<"' . $start_point . '" LIMIT 5');

    System::outjs(['s' => 'ok'], $nmch);
}

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);
$html->nav('Друзья');

$owners = $sql->query('SELECT `id`, `user`, `rights`, `time` FROM `owners` WHERE `server`="' . $id . '" AND `time`>"' . $start_point . '" ORDER BY `id` ASC LIMIT 5');

if ($sql->num()) {
    include(LIB . 'games/games.php');
}

while ($owner = $sql->get($owners)) {
    $sql->query('SELECT `login` FROM `users` WHERE `id`="' . $owner['user'] . '" LIMIT 1');
    if (!$sql->num()) {
        continue;
    }

    $uowner = $sql->get();

    $rights = games::owners(System::b64djs($owner['rights']));

    $html->get('owners', 'sections/servers/games/owners');
    $html->set('id', $id);
    $html->set('oid', $owner['id']);
    $html->set('user', $uowner['login']);
    $html->set('rights', $rights);
    $html->set('time', date('d.m.Y - H:i', $owner['time']));
    $html->pack('owners');
}

foreach ($aOwnersI as $access => $info) {
    $html->get('access', 'sections/servers/games/owners');
    $html->set('access', $access);
    $html->set('info', $info);
    $html->pack('access');
}

$html->get('index', 'sections/servers/games/owners');
$html->set('id', $id);
$html->set('time', date('d.m.Y', $start_point));
$html->set('access', $html->arr['access']);
$html->set('owners', $html->arr['owners'] ?? 'Для данного сервера совладельцы отсутсвуют.');
$html->pack('main');
