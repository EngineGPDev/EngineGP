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

$name_mcache = 'cashback_' . $user['id'];

// Проверка сессии
if ($mcache->get($name_mcache)) {
    System::outjs(['e' => $text['mcache']], $name_mcache);
}

// Создание сессии
$mcache->set($name_mcache, 1, false, 10);

if (!$cfg['part_money']) {
    System::outjs(['e' => 'Вывод средств невозможен'], $name_mcache);
}

$aData = [];

$aData['purse'] = isset($url['purse']) ? strtolower(trim($url['purse'])) : System::outjs(['e' => 'Необходимо указать кошелек'], $name_mcache);
$aData['sum'] = isset($url['sum']) ? round(floatval($url['sum']), 2) : System::outjs(['e' => 'Необходимо указать сумму'], $name_mcache);

$sql->query('SELECT `part_money` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
$user = array_merge($user, $sql->get());

// Проверка доступной суммы
if ($aData['sum'] > $user['part_money']) {
    System::outjs(['e' => 'У вас нет указанной суммы'], $name_mcache);
}

if (!in_array($aData['purse'], ['phone', 'wmr', 'lk'])) {
    System::outjs(['e' => 'Неверно указан кошелек'], $name_mcache);
}

// Вывод на баланс сайта
if ($aData['purse'] == 'lk') {
    if ($aData['sum'] < 1) {
        System::outjs(['e' => 'Сумма не должна быть меньше 1 ' . $cfg['currency']], $name_mcache);
    }

    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] + $aData['sum']) . '", `part_money`="' . ($user['part_money'] - $aData['sum']) . '" WHERE `id`="' . $user['id'] . '"');
    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(
        System::text('logs', 'cashback'),
        ['purse' => $cfg['part_log'], 'money' => $aData['sum']]
    ) . '", `date`="' . $start_point . '", `type`="cashback", `money`="' . $aData['sum'] . '"');

    System::outjs(['s' => 'Перевод средств был успешно произведен'], $name_mcache);
}

// Проверка лимита на мин. сумму за перевод
if ($aData['sum'] < $cfg['part_limit_min']) {
    System::outjs(['e' => 'Миниммальная сумма вывода ' . $cfg['part_limit_min'] . ' ' . $cfg['currency']], $name_mcache);
}

// Проверка кошелька
if ($aData['purse'] == 'wmr') {
    $sql->query('SELECT `wmr` FROM `users` WHERE `id`="' . $user['id'] . '" AND `wmr`!="" LIMIT 1');
    if (!$sql->num()) {
        System::outjs(['e' => 'Чтобы вывести деньги на WMR-кошелек, необходимо его указать в профиле'], $name_mcache);
    }
} else {
    $sql->query('SELECT `phone` FROM `users` WHERE `id`="' . $user['id'] . '" AND `confirm_phone`="1" LIMIT 1');
    if (!$sql->num()) {
        System::outjs(['e' => 'Чтобы вывести деньги на QIWI, необходим подтвержденный номер в профиле'], $name_mcache);
    }
}

$purse = $sql->get();

// Вывод без одобрения
if ($cfg['part_output']) {
    // Проверка лимита на макс. сумму за 24 часа
    $sql->query('SELECT SUM(`money`) FROM `cashback` WHERE  `user`="' . $user['id'] . '" AND `time`<"' . ($start_point - 86400) . '" AND `status`="0"');
    $sum = $sql->get();

    if (($aData['sum'] + $sum['SUM(`money`)']) > $cfg['part_limit_max']) {
        System::outjs(['e' => 'Максимальная сумма вывода за 24 часа ' . $cfg['part_limit_max'] . ' ' . $cfg['currency']], $name_mcache);
    }

    // Проверка общего лимита за 24 часа
    $sql->query('SELECT SUM(`money`) FROM `cashback` WHERE `time`<"' . ($start_point - 86400) . '" AND `status`="0"');
    $sum = $sql->get();

    if (($aData['sum'] + $sum['SUM(`money`)']) > $cfg['part_limit_day']) {
        System::outjs(['e' => 'Общий лимит на вывод за 24 часа достигнут, попробуйте вывести завтра'], $name_mcache);
    }

    // Запрос на шлюз
    if ($cfg['part_gateway'] == 'unitpay') {
        $aType = ['phone' => 'qiwi', 'wmr' => 'webmoney'];

        $sql->query('INSERT INTO `cashback` set `user`="' . $user['id'] . '", `purse`="' . $purse[$aData['purse']] . '", `money`="' . $aData['sum'] . '", `date`="' . $start_point . '", `status`="0"');
        $id = $sql->id();

        $sum = $aData['sum'] - ($aData['sum'] / 100 * $cfg['part_output_proc']);

        $json = file_get_contents('https://unitpay.ru/api?method=massPayment&params[sum]=' . $sum . '&params[purse]=' . $purse[$aData['purse']] . '&params[login]=' . $cfg['unitpay_mail'] . '&params[transactionId]=' . $id . ' &params[secretKey]=' . $cfg['unitpay_api'] . '&params[paymentType]=' . $aType[$aData['purse']]);

        $array = json_decode($json, true);

        // Упешный вывод средств
        if (is_array($array) and isset($array['result']) and in_array($array['result']['status'], ['success', 'not_completed '])) {
            $sql->query('UPDATE `users` set `part_money`="' . ($user['part_money'] - $aData['sum']) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
            $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(
                System::text('logs', 'cashback'),
                ['purse' => $aType[$aData['purse']], 'money' => $aData['sum']]
            ) . '", `date`="' . $start_point . '", `type`="cashback", `money`="' . $aData['sum'] . '"');

            System::outjs(['s' => 'Запрос на вывод средств был успешно выполнен'], $name_mcache);
        }

        if (!is_array($array)) {
            System::outjs(['e' => 'Неудалось выполнить запрос'], $name_mcache);
        }

        switch ($array['error']['code']) {
            case '103':
                System::outjs(['e' => 'На данный момент вы не можете вывести средства, обратитесь к администратору'], $name_mcache);
                // no break
            case '104':
                System::outjs(['e' => 'Номер телефона не входит в список доступных для выплат стран'], $name_mcache);
                // no break
            case '1053':
                System::outjs(['e' => 'Платежная система не смогла получить информацию о номере телефона'], $name_mcache);
        }
    }

    System::outjs(['e' => 'Технические проблемы, обратитесь в службу поддержки' . $array['error']['code']], $name_mcache);
}

$sql->query('UPDATE `users` set `part_money`="' . ($user['part_money'] - $aData['sum']) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
$sql->query('INSERT INTO `cashback` set `user`="' . $user['id'] . '", `purse`="' . $purse[$aData['purse']] . '", `money`="' . $aData['sum'] . '", `date`="' . $start_point . '", `status`="1"');

System::outjs(['s' => 'Заявка на вывод средств была успешно создана'], $name_mcache);
