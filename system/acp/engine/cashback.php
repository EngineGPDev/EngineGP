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

use EngineGP\AdminSystem;
use Symfony\Component\Dotenv\Dotenv;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Загружаем .env
$dotenv = new Dotenv();
$dotenv->load(ROOT . '/.env');

if ($_ENV['RUN_MODE'] === 'dev') {
    // Включение отображения ошибок в режиме разработки
    ini_set('display_errors', true);
    ini_set('html_errors', true);
    ini_set('error_reporting', E_ALL);
} else {
    // Отключение отображения ошибок в продакшене
    ini_set('display_errors', false);
    ini_set('html_errors', false);
    ini_set('error_reporting', 0);
}

$nmc = 'cashback_' . $id;

// Проверка сессии
if ($mcache->get($nmc)) {
    AdminSystem::outjs(['e' => $text['mcache']], $nmc);
}

// Создание сессии
$mcache->set($nmc, 1, false, 10);

if ($id) {
    $sql->query('SELECT `user`, `money`, `purse`, `status` FROM `cashback` WHERE `id`="' . $id . '" LIMIT 1');
    $cb = $sql->get();

    if (!$cb['status']) {
        AdminSystem::outjs(['e' => 'Данная заявка уже была обработана'], $nmc);
    }

    $purse = $cb['purse'][0] == 'R' ? 'webmoney' : 'qiwi';

    // Запрос на шлюз
    if ($cfg['part_gateway'] == 'unitpay') {
        $sum = $cb['money'] - ($cb['money'] / 100 * $cfg['part_output_proc']);

        $json = file_get_contents('https://unitpay.ru/api?method=massPayment&params[sum]=' . $sum . '&params[purse]=' . $cb['purse'] . '&params[login]=' . $cfg['unitpay_mail'] . '&params[transactionId]=' . $id . ' &params[secretKey]=' . $cfg['unitpay_api'] . '&params[paymentType]=' . $purse);

        $array = json_decode($json, true);

        // Упешный вывод средств
        if (is_array($array) and isset($array['result']) and in_array($array['result']['status'], ['success', 'not_completed '])) {
            $sql->query('UPDATE `cashback` set `status`="0" WHERE `id`="' . $id . '" LIMIT 1');
            $sql->query('INSERT INTO `logs` set `user`="' . $cb['user'] . '", `text`="' . AdminSystem::updtext(
                AdminSystem::text('logs', 'cashback'),
                ['purse' => $purse, 'money' => $cb['money']]
            ) . '", `date`="' . $start_point . '", `type`="cashback", `money`="' . $cb['money'] . '"');

            AdminSystem::outjs(['s' => 'Запрос на вывод средств был успешно выполнен'], $nmc);
        }

        if (!is_array($array)) {
            AdminSystem::outjs(['e' => 'Неудалось выполнить запрос'], $nmc);
        }

        AdminSystem::outjs(['e' => $array['error']['message']], $nmc);
    }

    $sql->query('UPDATE `cashback` set `status`="0" WHERE `id`="' . $id . '" LIMIT 1');
    $sql->query('INSERT INTO `logs` set `user`="' . $cb['user'] . '", `text`="' . AdminSystem::updtext(
        AdminSystem::text('logs', 'cashback'),
        ['purse' => $purse, 'money' => $cb['money']]
    ) . '", `date`="' . $start_point . '", `type`="cashback", `money`="' . $cb['money'] . '"');

    AdminSystem::outjs(['s' => 'Запрос на вывод средств был успешно выполнен в ручном режиме'], $nmc);
}

AdminSystem::outjs(['e' => 'Не передан идентификатор заявки'], $nmc);
