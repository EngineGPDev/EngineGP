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

// Установка
if ($go) {
    if (!$aWeb[$server['game']][$url['subsection']]) {
        System::outjs(['e' => 'Дополнительная услуга недоступна для установки.'], $nmch);
    }

    // Проверка на наличие уже установленной выбранной услуги
    switch ($aWebInstall[$server['game']][$url['subsection']]) {
        case 'server':
            $sql->query('SELECT `id` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');
            break;

        case 'user':
            $sql->query('SELECT `id` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');
            break;

        case 'unit':
            $sql->query('SELECT `id` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');
            break;
    }

    if ($sql->num()) {
        System::outjs(['s' => 'ok'], $nmch);
    }

    include(LIB . 'ssh.php');

    if ($aWebUnit['unit'][$url['subsection']] == 'local') {
        $sql->query('SELECT `address`, `passwd`, `domain` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        $pma = $unit['domain'];
    } else {
        $unit = [
            'address' => $aWebUnit['address'],
            'passwd' => $aWebUnit['passwd'],
        ];

        $pma = $aWebUnit['pma'];
    }

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        System::outjs(['e' => System::text('ssh', 'error')], $nmch);
    }

    if (isset($_POST['passwd'])) {
        // Если не указан пароль сгенерировать
        if ($_POST['passwd'] == '') {
            $passwd = System::passwd($aWebParam[$url['subsection']]['passwd']);
        } else {
            // Проверка длинны пароля
            if (!isset($_POST['passwd'][5]) || isset($_POST['passwd'][16])) {
                System::outjs(['e' => 'Необходимо указать пароль длинной не менее 6-и символов и не более 16-и.'], $nmch);
            }

            // Проверка валидности пароля
            if (System::valid($_POST['passwd'], 'other', "/^[A-Za-z0-9]{6,16}$/")) {
                System::outjs(['e' => 'Пароль должен состоять из букв a-z и цифр.'], $nmch);
            }

            $passwd = $_POST['passwd'];
        }
    } else {
        $passwd = System::passwd($aWebParam[$url['subsection']]['passwd']);
    }

    $sql->query('INSERT INTO `web` set `type`="' . $url['subsection'] . '", `server`="' . $id . '", `user`="' . $server['user'] . '", `unit`="' . $server['unit'] . '", `config`=""');
    $wid = $sql->id();
    $uid = $wid + 10000;

    // Данные
    $login = substr('sql_' . $wid . '_free', 0, 14);

    $sql_q = 'mysql --login-path=local -e "CREATE DATABASE ' . $login . ';'
        . "CREATE USER '" . $login . "'@'%' IDENTIFIED BY '" . $passwd . "';"
        . 'GRANT ALL PRIVILEGES ON ' . $login . ' . * TO \'' . $login . '\'@\'%\';";';

    $ssh->set($sql_q);

    // Обновление данных
    $sql->query('UPDATE `web` set `uid`="' . $uid . '",'
        . '`domain`="' . $pma . '",'
        . '`passwd`="' . $passwd . '",'
        . '`login`="' . $login . '", `date`="' . $start_point . '" '
        . 'WHERE `id`="' . $wid . '" LIMIT 1');

    System::outjs(['s' => 'ok'], $nmch);
}

$html->nav('Установка ' . $aWebname[$url['subsection']]);

$html->get('install', 'sections/web/' . $url['subsection'] . '/free');

$html->set('id', $id);

$html->pack('main');
