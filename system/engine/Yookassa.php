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

use YooKassa\Client;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$client = new Client();
$client->setAuth($_ENV['YOOKASSA_SHOPID'], $_ENV['YOOKASSA_SECRETKEY']);

// Получаем параметры из URL
$userId = intval($_GET['user']);
$amount = floatval($_GET['amount']);

// Проверяем, валиден ли userId
$sql->query('SELECT `id`, `balance`, `part` FROM `users` WHERE `id`="' . $userId . '" LIMIT 1');
if (!$sql->num()) {
    sys::out('bad user');
}

$user = $sql->get();

try {
    // Создаем платеж с использованием полученной суммы
    $payment = $client->createPayment(
        [
            'amount' => array(
                'value' => $amount,
                'currency' => 'RUB',
            ),
            'confirmation' => array(
                'type' => 'redirect',
                'return_url' => $cfg['http'] . 'success.html',
            ),
            'capture' => true,
            'description' => 'Пополнение счёта',
            'metadata' => [
                'userId' => $user['id'],
            ],
        ],
        uniqid('', true)
    );

    if ($payment->getStatus() === 'pending') {
        sys::outjs(['payLink' => $payment->getConfirmation()->getConfirmationUrl()]);
    }
} catch (\Exception $e) {
    sys::outjs(['Error' => $e->getMessage()]);
}
