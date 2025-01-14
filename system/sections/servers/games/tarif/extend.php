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

$promo = false;

// Цена аренды за выбранный период (promo -> с учетом промо-кода)
if (isset($url['promo']) || $aData['promo'] != '') {
    $promo = games::define_promo(
        $aData['promo'],
        $tarif['discount'],
        $sum,
        $aData,
        'extend'
    );
}

// Использование промо-кода
if (is_array($promo)) {
    if (array_key_exists('sum', $promo)) {
        $sum = $promo['sum'];
    } else {
        $aData['time'] += $promo['days'] * 86400;
    } // Кол-во дней аренды с учетом подарочных (промо-код)
}

// Выполнение продления
if ($go) {
    // Проверка баланса
    if ($user['balance'] < $sum) {
        sys::outjs(['e' => 'У вас не хватает ' . (round($sum - $user['balance'], 2)) . ' ' . $cfg['currency']], $nmch);
    }

    // Списание средств с баланса пользователя
    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $sum) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Реф. система
    games::part($user['id'], $sum);

    $status = $server['status'] == 'overdue' ? '`status`="off",' : '';

    // Время аренды
    $time = $server['time'] < $start_point ? $start_point + $aData['time'] * 86400 : $server['time'] + $aData['time'] * 86400;

    // Обновление информации
    $sql->query('UPDATE `servers` set ' . $status . ' `time`="' . $time . '", `test`="0" WHERE `id`="' . $id . '" LIMIT 1');

    // Продление адреса на 30 дней
    if ($add_sum) {
        $sql->query('UPDATE `address_buy` set `time`=`time`+"2592000" WHERE `server`="' . $id . '" LIMIT 1');
    }

    // Запись логов
    if (!is_array($promo)) {
        $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . sys::updtext(
            sys::text('logs', 'extend_server'),
            ['days' => $days,
                    'money' => $sum,
                    'id' => $id]
        ) . '", `date`="' . $start_point . '", `type`="extend", `money`="' . $sum . '"');
    } else {
        $sql->query('UPDATE `servers` set `benefit`="' . $time . '" WHERE `id`="' . $id . '" LIMIT 1');
        $sql->query('INSERT INTO `promo_use` set `promo`="' . $promo['id'] . '", `user`="' . $user['id'] . '", `time`="' . $start_point . '"');
        $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . sys::updtext(
            sys::text('logs', 'extend_server_promo'),
            ['days' => $days,
                    'money' => $sum,
                    'promo' => $promo['cod'], 'id' => $id]
        ) . '", `date`="' . $start_point . '", `type`="extend", `money`="' . $sum . '"');
    }

    sys::outjs(['s' => 'ok'], $nmch);
}

// Выхлоп цены
sys::outjs(['s' => $sum]);
