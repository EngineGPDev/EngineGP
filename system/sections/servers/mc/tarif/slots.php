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

// Если фикс. значение слот
if ($tarif['slots_min'] == $tarif['slots_max']) {
    System::outjs(['e' => 'На данном тарифе нельзя изменить количество слот.'], $nmch);
}

$slots = isset($url['slots']) ? System::int($url['slots']) : System::outjs(['e' => 'Переданы не все данные.'], $nmch);

$aPrice = explode(':', $tarif['price']);
$aRAM = explode(':', $tarif['ram']);

$overdue = $server['time'] < $start_point;

if ($cfg['change_slots'][$server['game']]['days'] || $overdue) {
    // Цена за 1 день
    $price = $aPrice[array_search($server['ram'], $aRAM)] / 30;

    // Цена аренды за остаток дней (с текущим кол-вом слот)
    $price_old = ($server['time'] - $start_point) / 86400 * $price * $server['slots'];
}

$max = $tarif['slots_max'] - $server['slots'];

// Сумма за добавляемые слоты
$sum = round(($server['time'] - $start_point) / 86400 * ($aPrice[array_search($server['ram'], $aRAM)] / 30) * $slots, 2);

// Изменение кол-ва слот за счет пересчета дней аренды или закончился срок аренды (иначе аренда дополнительных слот)
if ($cfg['change_slots'][$server['game']]['days'] || $overdue) {
    // Если просрочен
    if ($overdue) {
        System::outjs(['i' => '']);

        if ($go) {
            $start = $server['slots_start'] > $slots ? ', `slots_start`="' . $slots . '"' : '';

            $sql->query('UPDATE `servers` set `slots`="' . $slots . '" ' . $start . ', `ram`=' . $server['ram'] . ' WHERE `id`="' . $id . '" LIMIT 1');

            // Запись логов
            $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . System::text('syslogs', 'change_slots') . '", `time`="' . $start_point . '"');

            System::outjs(['s' => 'ok'], $nmch);
        }
    }

    // При возможности уменьшить
    if ($cfg['change_slots'][$server['game']]['down'] || $overdue) {
        // Проверка кол-ва слот
        if ($slots < $tarif['slots_min'] || $slots > $tarif['slots_max']) {
            System::outjs(['e' => 'Переданые неверные данные.'], $nmch);
        }

        if ($server['slots'] == $slots) {
            if ($go) {
                System::outjs(['s' => 'ok'], $nmch);
            }

            System::outjs(['s' => 'Сервер будет арендован до: ' . date('d.m.Y - H:i', $server['time']) . ' (' . System::date('min', $server['time']) . ')'], $nmch);
        }
    } else {
        // Установлено макс. значение
        if ($server['slots'] == $tarif['slots_max'] and !$overdue) {
            System::outjs(['e' => 'На игровом сервере установлено максимальное значение.'], $nmch);
        }

        if ($slots < 1 || $slots > $max) {
            System::outjs(['e' => 'Переданы неверные данные'], $nmch);
        }

        $slots += $server['slots'];
    }

    $date = date('H.i.s.d.m.Y', round($start_point + $price_old / ($price * $slots) * 86400 - 86400));

    $aDate = explode('.', $date);

    $time = mktime($aDate[0], $aDate[1], $aDate[2], $aDate[4], $aDate[3], $aDate[5]);

    // При уменьшении кол-ва слот не добавлять дни
    if ($slots < $server['slots'] and ($cfg['change_slots'][$server['game']]['days'] and $cfg['change_slots'][$server['game']]['down'] and !$cfg['change_slots'][$server['game']]['add'])) {
        $time = $server['time'];
    }

    // Выполнение операции
    if ($go) {
        System::benefitblock($id, $nmch);

        $start = $server['slots_start'] > $slots ? ', `slots_start`="' . $slots . '"' : '';

        $sql->query('UPDATE `servers` set `time`="' . $time . '", `slots`="' . $slots . '" ' . $start . ', `ram`=' . $server['ram'] . ' WHERE `id`="' . $id . '" LIMIT 1');

        if (in_array($server['status'], ['working', 'start', 'restart']) and $slots < $server['slots_start']) {
            include(LIB . 'games/' . $server['game'] . '/action.php');

            action::start($id, 'restart');
        }

        // Запись логов
        $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . System::text('syslogs', 'change_slots') . '", `time`="' . $start_point . '"');

        System::outjs(['s' => 'ok'], $nmch);
    }

    // Выхлоп информации
    System::outjs(['s' => 'Сервер будет арендован до: ' . date('d.m.Y - H:i', $time) . ' (' . System::date('min', $time) . ')']);
}

if ($slots < 1 || $slots > $max) {
    System::outjs(['e' => 'Переданые неверные данные'], $nmch);
}

// Выполнение операции
if ($go) {
    System::benefitblock($id, $nmch);

    $slots_new = $server['slots'] + $slots;

    // Проверка баланса
    if ($user['balance'] < $sum) {
        System::outjs(['e' => 'У вас не хватает ' . (round($sum - $user['balance'], 2)) . ' ' . $cfg['currency']], $nmch);
    }

    // Списание средств с баланса пользователя
    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $sum) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Реф. система
    Game::part($user['id'], $sum);

    $start = $server['slots_start'] == $server['slots'] ? ', `slots_start`="' . $slots_new . '"' : '';

    // Обновление информации
    $sql->query('UPDATE `servers` set `slots`="' . $slots_new . '" ' . $start . ', `ram`=' . $server['ram'] . ' WHERE `id`="' . $id . '" LIMIT 1');

    if (in_array($server['status'], ['working', 'start', 'restart']) and $slots_new != $server['slots_start']) {
        include(LIB . 'games/' . $server['game'] . '/action.php');

        action::start($id, 'restart');
    }

    // Запись логов
    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(
        System::text('logs', 'buy_slots'),
        ['slots' => $slots, 'money' => $sum, 'id' => $id]
    ) . '", `date`="' . $start_point . '", `type`="buy", `money`="' . $sum . '"');

    System::outjs(['s' => 'ok'], $nmch);
}

// Выхлоп информации
System::outjs(['s' => 'Цена за дополнительные слоты: ' . $sum . ' ' . $cfg['currency']]);
