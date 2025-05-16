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

// Выполнение продления
if ($go) {
    $sql->query('SELECT `id`, `aid`, `time` FROM `address_buy` WHERE `server`="' . $id . '" LIMIT 1');

    if (!$sql->num()) {
        System::outjs(['s' => 'ok'], $nmch);
    }

    $add = $sql->get();

    $sql->query('SELECT `price` FROM `address` WHERE `id`="' . $add['aid'] . '" LIMIT 1');

    $add = array_merge($add, $sql->get());

    // Проверка баланса
    if ($user['balance'] < $add['price']) {
        System::outjs(['e' => 'У вас не хватает ' . (round($add['price'] - $user['balance'], 2)) . ' ' . $cfg['currency']], $nmch);
    }

    // Списание средств с баланса пользователя
    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $add['price']) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Реф. система
    Game::part($user['id'], $add['price']);

    // Обновление информации
    $sql->query('UPDATE `address_buy` set `time`="' . ($add['time'] + 2592000) . '" WHERE `id`="' . $add['id'] . '" LIMIT 1');

    // Запись логов
    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(
        System::text('logs', 'extend_address'),
        ['money' => $add['price'], 'id' => $id]
    ) . '", `date`="' . $start_point . '", `type`="extend", `money`="' . $add['price'] . '"');

    System::outjs(['s' => 'ok'], $nmch);
}
