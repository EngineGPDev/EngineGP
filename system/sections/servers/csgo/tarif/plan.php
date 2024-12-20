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

if (!isset($nmch)) {
    $nmch = false;
}

$plan = isset($url['plan']) ? sys::int($url['plan']) : sys::outjs(['e' => 'Переданые не все данные'], $nmch);

$aPrice = explode(':', $tarif['price']);
$aTICK = explode(':', $tarif['tickrate']);

// Проверка плана
if (array_search($plan, $aTICK) === false) {
    sys::outjs(['e' => 'Переданы неверные данные'], $nmch);
}

if ($plan == $server['tickrate']) {
    sys::outjs(['e' => 'Смысла в этой операции нет'], $nmch);
}

if (!tarif::price($tarif['price'])) {
    sys::outjs(['e' => 'Чтобы изменить тариф, перейдите в настройки запуска'], $nmch);
}

if ($server['time'] < $start_point + 86400) {
    $time = $server['time'];
} else {
    // Цена за 1 день аренды (по новому тарифному плану)
    $price = $aPrice[array_search($plan, $aTICK)] / 30 * $server['slots'];

    // Цена за 1 день аренды (по старому тарифному плану)
    $price_old = $aPrice[array_search($server['tickrate'], $aTICK)] / 30 * $server['slots'];

    // Остаток дней аренды
    $days = ($server['time'] - $start_point) / 86400;

    $time = date('H:i:s', $server['time']);
    $date = date('d.m.Y', round($start_point + $days * $price_old / $price * 86400 - 86400));

    $aDate = explode('.', $date);
    $aTime = explode(':', $time);

    $time = mktime($aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2]);
}

// Выполнение смена тарифного плана
if ($go) {
    sys::benefitblock($id, $nmch);

    $sql->query('UPDATE `servers` set `time`="' . $time . '", `tickrate`="' . $plan . '" WHERE `id`="' . $id . '" LIMIT 1');

    if (in_array($server['status'], ['working', 'start', 'restart', 'change'])) {
        include(LIB . 'games/' . $server['game'] . '/action.php');

        action::start($id, 'restart');
    }

    // Запись логов
    $sql->query('INSERT INTO `logs_sys` set `user`="' . $user['id'] . '", `server`="' . $id . '", `text`="' . sys::text('syslogs', 'change_plan') . '", `time`="' . $start_point . '"');

    sys::outjs(['s' => 'ok'], $nmch);
}

// Выхлоп информации
sys::outjs(['s' => date('d.m.Y - H:i', $time) . ' (' . sys::date('min', $time) . ')'], $nmch);
