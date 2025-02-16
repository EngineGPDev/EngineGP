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

// Выполнение операции
if ($go) {
    if ($server['status'] != 'off') {
        System::outjs(['e' => 'Игровой сервер должен быть выключен'], $nmch);
    }

    $pack = $url['pack'] ?? System::outjs(['e' => 'Переданы не все данные.'], $nmch);

    // Проверка сборки
    if (!array_key_exists($pack, System::b64djs($tarif['packs'], true))) {
        System::outjs(['e' => 'Сборка не найдена.']);
    }

    $sql->query('SELECT `id`, `unit`, `port_min`, `port_max`, `hostname`, `path`, `install`, `map`, `plugins_install`, `hdd`, `autostop`, `ip` FROM `tarifs` WHERE `id`="' . $tarif['id'] . '" LIMIT 1');
    $tarif = array_merge(['pack' => $pack], $sql->get());

    $sql->query('SELECT `name`, `address`, `passwd` FROM `units` WHERE `id`="' . $tarif['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    // Выделенный адрес игрового сервера
    if (!empty($tarif['ip'])) {
        $aIp = explode(':', $tarif['ip']);

        $ip = false;
        $port = params::$aDefPort[$server['game']];

        // Проверка наличия свободного адреса
        foreach ($aIp as $adr) {
            $adr = trim($adr);

            $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $tarif['unit'] . '" AND `address` LIKE "' . $adr . ':%" LIMIT 1');
            if (!$sql->num()) {
                $ip = $adr;

                break;
            }
        }
    } else {
        $ip = System::first(explode(':', $unit['address']));
        $port = false;

        // Проверка наличия свободного порта
        for ($tarif['port_min']; $tarif['port_min'] <= $tarif['port_max']; $tarif['port_min'] += 1) {
            $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $tarif['unit'] . '" AND `port`="' . $tarif['port_min'] . '" LIMIT 1');
            if (!$sql->num()) {
                $port = $tarif['port_min'];

                break;
            }
        }
    }

    if (!$ip || !$port) {
        System::outjs(['e' => 'К сожалению нет доступных мест, обратитесь в тех.поддержку.']);
    }

    $server['id'] = $id;

    // Време аренды
    $tarif['time'] = $time;

    include(LIB . 'ssh.php');

    // Удаление данных с текущей локации
    tarif::unit_old($oldTarif, $oldUnit, $server, $nmch);

    $mcache->delete('server_filetp_' . $id);

    $adUnit = explode(':', $unit['address']);

    $server['address'] = $ip . ':' . $port;

    // Создание сервера на новой локации
    tarif::unit_new($tarif, $unit, $server, $nmch);

    // Запись логов
    $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . System::text('syslogs', 'change_unit') . '", `time`="' . $start_point . '"');

    System::outjs(['s' => 'ok'], $nmch);
}

// Генерация списка сборок
$packs = '';
$aPack = System::b64djs($tarif['packs'], true);

if (is_array($aPack)) {
    foreach ($aPack as $index => $name) {
        $packs .= '<option value="' . $index . '">' . $name . '</option>';
    }
}

// Выхлоп информации
System::outjs(['s' => date('d.m.Y - H:i', $time) . ' (' . System::date('min', $time) . ')', 'p' => $packs]);
