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
use EngineGP\Model\Game;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if (!isset($nmch)) {
    $nmch = false;
}

// Проверка наличия арендованного выделенного адреса
$sql->query('SELECT `id` FROM `address_buy` WHERE `server`="' . $id . '" LIMIT 1');
if ($sql->num() and $go) {
    System::outjs(['s' => 'ok'], $nmch);
}

$aid = isset($url['aid']) ? System::int($url['aid']) : System::outjs(['e' => 'Переданы не все данные'], $nmch);

$sql->query('SELECT `ip`, `price` FROM `address` WHERE `id`="' . $aid . '" AND `unit`="' . $server['unit'] . '" AND `buy`="0" LIMIT 1');

if (!$sql->num()) {
    System::outjs(['e' => 'Выделенный адрес не найден.'], $nmch);
}

$add = $sql->get();

// Выполнение операции
if ($go) {
    // Проверка баланса
    if ($user['balance'] < $add['price']) {
        System::outjs(['e' => 'У вас не хватает ' . (round($add['price'] - $user['balance'], 2)) . ' ' . $cfg['currency']], $nmch);
    }

    include(LIB . 'ssh.php');

    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    // Проверка ssh соединения с локацией
    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        System::outjs(['e' => System::text('error', 'ssh')], $nmch);
    }

    // Списание средств с баланса пользователя
    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $add['price']) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Реф. система
    Game::part($user['id'], $add['price']);

    // Обновление информации
    $sql->query('UPDATE `address` set `buy`="1" WHERE `id`="' . $aid . '" LIMIT 1');
    $sql->query('UPDATE `servers` set `address`="' . $add['ip'] . ':' . params::$aDefPort[$server['game']] . '" WHERE `id`="' . $id . '" LIMIT 1');

    $sql->query('INSERT INTO `address_buy` set `aid`="' . $aid . '", `server`="' . $id . '", `time`="' . ($start_point + 2592000) . '"');

    // Порт игрового сервера
    $port = explode(':', $server['address']);

    // Очистка правил FireWall
    Game::iptables($server['id'], 'remove', null, null, null, null, false, $ssh);

    // Запись логов
    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(
        System::text('logs', 'buy_address'),
        ['money' => $add['price'], 'id' => $id]
    ) . '", `date`="' . $start_point . '", `type`="buy", `money`="' . $add['price'] . '"');

    System::outjs(['s' => 'ok'], $nmch);
}

System::outjs(['s' => $add['price']]);
