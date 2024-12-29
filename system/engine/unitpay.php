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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

function md5sign($params, $secretKey)
{
    ksort($params);
    unset($params['sign']);

    return md5(join(null, $params) . $secretKey);
}

function getSignature($method, $params, $secretKey)
{
    ksort($params);
    unset($params['sign']);
    unset($params['signature']);
    array_push($params, $secretKey);
    array_unshift($params, $method);

    return hash('sha256', join('{up}', $params));
}

$unitpayIp = ['31.186.100.49', '178.132.203.105', '52.29.152.23', '52.19.56.234'];

if (!in_array($uip, $unitpayIp)) {
    sys::outjs(['error' => ['message' => 'Некорректный адрес сервера']]);
}

$secretKey = $cfg['unitpay_key'];
$params = $_GET['params'];

if ($params['signature'] != getSignature($_GET['method'], $params, $secretKey)) {
    sys::outjs(['error' => ['message' => 'Некорректная цифровая подпись']]);
}

if (!in_array($_GET['method'], ['pay', 'check', 'error'])) {
    sys::outjs(['error' => ['message' => 'Некорректный метод']]);
}

// Оплата по ключу
if (!sys::valid($params['account'], 'md5')) {
    $sql->query('SELECT `id`, `server`, `price` FROM `privileges_buy` WHERE `key`="' . $params['account'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['error' => ['message' => 'bad key: ' . $params['account']]]);
    }

    $privilege = $sql->get();

    $money = round($params['sum'] * $cfg['curinrub'], 2);

    if ($money < $privilege['price']) {
        sys::outjs(['error' => ['message' => 'bad sum']]);
    }

    $sql->query('SELECT `user` FROM `servers` WHERE `id`="' . $privilege['server'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['error' => ['message' => 'bad server']]);
    }

    $server = $sql->get();

    $sql->query('SELECT `id`, `balance`, `part_money` FROM `users` WHERE `id`="' . $server['user'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['error' => ['message' => 'bad owner']]);
    }

    if (isset($_GET['method']) and $_GET['method'] == 'check') {
        sys::outjs(['result' => ['message' => 'Запрос успешно обработан']]);
    }

    $user = $sql->get();

    if ($cfg['part_money']) {
        $sql->query('UPDATE `users` set `part_money`="' . ($user['part_money'] + $money) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
    } else {
        $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] + $money) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
    }

    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . sys::updtext(
        sys::text('logs', 'profit'),
        ['server' => $privilege['server'], 'money' => $money]
    ) . '", `date`="' . $start_point . '", `type`="part", `money`="' . $money . '"');

    $sql->query('UPDATE `privileges_buy` set `status`="1" WHERE `id`="' . $privilege['id'] . '" LIMIT 1');

    sys::outjs(['result' => ['message' => 'Запрос успешно обработан']]);
}

switch ($_GET['method']) {
    case 'pay':
        $sum = round($params['sum'], 2);

        $user = intval($params['account']);

        $sql->query('SELECT `id`, `balance`, `part` FROM `users` WHERE `id`="' . $user . '" LIMIT 1');
        if (!$sql->num()) {
            sys::outjs(['result' => ['message' => 'Пользователь c ID: ' . $user . ' не найден']]);
        }

        $user = $sql->get();

        $money = round($user['balance'] + $sum * $cfg['curinrub'], 2);

        if ($cfg['part']) {
            $part_sum = round($sum / 100 * $cfg['part_proc'], 2);

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
                    ['part' => $uid, 'money' => $part_sum]
                ) . '", `date`="' . $start_point . '", `type`="part", `money`="' . $part_sum . '"');
            }
        }

        $sql->query('UPDATE `users` set `balance`="' . $money . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

        $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="Пополнение баланса на сумму: ' . $sum . ' ' . $cfg['currency'] . '", `date`="' . $start_point . '", `type`="replenish", `money`="' . $sum . '"');

        sys::outjs(['result' => ['message' => 'Запрос успешно обработан']]);

        // no break
    case 'check':
        $sql->query('SELECT `id` FROM `users` WHERE `id`="' . intval($params['account']) . '" LIMIT 1');
        if ($sql->num()) {
            sys::outjs(['result' => ['message' => 'Запрос успешно обработан']]);
        }

        sys::outjs(['jsonrpc' => "2.0", 'error' => ['code' => -32000, 'message' => 'Пользователь не найден'], 'id' => 1]);

        // no break
    case 'error':
        sys::outjs(['result' => ['message' => 'Запрос успешно обработан']]);
}
