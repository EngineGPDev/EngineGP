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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Получаем тело запроса (JSON)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Проверяем тип события и статус платежа
if (isset($data['event']) && $data['event'] === 'payment.succeeded') {
    $paymentId = $data['object']['id'];
    $userId = intval($data['object']['metadata']['userId']);
    $amount = floatval($data['object']['amount']['value']);

    $sql->query('SELECT `id`, `balance`, `part` FROM `users` WHERE `id`="' . $userId . '" LIMIT 1');
    if (!$sql->num()) {
        http_response_code(400);
        exit;
    }

    $user = $sql->get();
    $newBalance = round($user['balance'] + $amount * $cfg['curinrub'], 2);

    if ($cfg['part']) {
        $part_sum = round($amount / 100 * $cfg['part_proc'], 2);

        $sql->query('SELECT `balance`, `part_money` FROM `users` WHERE `id`="' . $user['part'] . '" LIMIT 1');
        if ($sql->num()) {
            $part = $sql->get();

            if ($cfg['part_money']) {
                $sql->query('UPDATE `users` set `part_money`="' . ($part['part_money'] + $part_sum) . '" WHERE `id`="' . $user['part'] . '" LIMIT 1');
            } else {
                $sql->query('UPDATE `users` set `balance`="' . ($part['balance'] + $part_sum) . '" WHERE `id`="' . $user['part'] . '" LIMIT 1');
            }

            $sql->query('INSERT INTO `logs` set `user`="' . $user['part'] . '", `text`="' . sys::updtext(
                sys::text('logs', 'part'),
                ['part' => $user['part'], 'money' => $part_sum]
            ) . '", `date`="' . $start_point . '", `type`="part", `money`="' . $part_sum . '"');
        }
    }

    $sql->query('UPDATE `users` SET `balance`="' . $newBalance . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
    $sql->query('INSERT INTO `logs` SET `user`="' . $user['id'] . '", `text`="Пополнение баланса на сумму: ' . $amount . ' RUB", `date`="' . time() . '", `type`="replenish", `money`="' . $amount . '"');

    http_response_code(200);
    sys::outjs(['status' => 'ok']);
} else {
    http_response_code(400);
    sys::outjs(['status' => 'ignored']);
}
