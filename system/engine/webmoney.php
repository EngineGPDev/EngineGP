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

$check = strtoupper(hash('sha256', $_POST['LMI_PAYEE_PURSE']
    . $_POST['LMI_PAYMENT_AMOUNT']
    . $_POST['LMI_PAYMENT_NO']
    . $_POST['LMI_MODE']
    . $_POST['LMI_SYS_INVS_NO']
    . $_POST['LMI_SYS_TRANS_NO']
    . $_POST['LMI_SYS_TRANS_DATE']
    . $cfg['webmoney_key']
    . $_POST['LMI_PAYER_PURSE']
    . $_POST['LMI_PAYER_WM']));

if ($_POST['LMI_HASH'] != $check) {
    sys::out('bad hash');
}

if (!isset($_POST['LMI_PAYMENT_AMOUNT'])) {
    sys::out('bad amount');
}

$sum = round($_POST['LMI_PAYMENT_AMOUNT'], 2);

// Оплата по ключу
if (!sys::valid($_POST['us_user'], 'md5')) {
    $sql->query('SELECT `id`, `server`, `price` FROM `privileges_buy` WHERE `key`="' . $_POST['us_user'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::out('bad key');
    }

    $privilege = $sql->get();

    $money = round($sum * $cfg['curinrub'], 2);

    if ($money < $privilege['price']) {
        sys::out('bad sum');
    }

    $sql->query('SELECT `user` FROM `servers` WHERE `id`="' . $privilege['server'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::out('bad server');
    }

    $server = $sql->get();

    $sql->query('SELECT `id`, `balance`, `part_money` FROM `users` WHERE `id`="' . $server['user'] . '" LIMIT 1');
    if (!$sql->num()) {
        sys::out('bad owner');
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

    sys::out('success');
}

$user = intval($_POST['us_user']);

$sql->query('SELECT `id`, `balance`, `part` FROM `users` WHERE `id`="' . $user . '" LIMIT 1');
if (!$sql->num()) {
    sys::out('bad user');
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

sys::out('success');
