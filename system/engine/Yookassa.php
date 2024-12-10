<?php

/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
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
