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

// Выполнение продления
if ($go) {
    $sql->query('SELECT `id`, `aid`, `time` FROM `address_buy` WHERE `server`="' . $id . '" LIMIT 1');

    if (!$sql->num()) {
        sys::outjs(['s' => 'ok'], $nmch);
    }

    $add = $sql->get();

    $sql->query('SELECT `price` FROM `address` WHERE `id`="' . $add['aid'] . '" LIMIT 1');

    $add = array_merge($add, $sql->get());

    // Проверка баланса
    if ($user['balance'] < $add['price']) {
        sys::outjs(['e' => 'У вас не хватает ' . (round($add['price'] - $user['balance'], 2)) . ' ' . $cfg['currency']], $nmch);
    }

    // Списание средств с баланса пользователя
    $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $add['price']) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

    // Реф. система
    games::part($user['id'], $add['price']);

    // Обновление информации
    $sql->query('UPDATE `address_buy` set `time`="' . ($add['time'] + 2592000) . '" WHERE `id`="' . $add['id'] . '" LIMIT 1');

    // Запись логов
    $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . sys::updtext(
        sys::text('logs', 'extend_address'),
        ['money' => $add['price'], 'id' => $id]
    ) . '", `date`="' . $start_point . '", `type`="extend", `money`="' . $add['price'] . '"');

    sys::outjs(['s' => 'ok'], $nmch);
}
