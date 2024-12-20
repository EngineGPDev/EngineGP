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

// Если фикс. значение слот
if ($tarif['slots_min'] == $tarif['slots_max']) {
    sys::outjs(['e' => 'На данном тарифе нельзя изменить количество слот.'], $nmch);
}

$slots = isset($url['slots']) ? sys::int($url['slots']) : sys::outjs(['e' => 'Переданы не все данные.'], $nmch);

$aPrice = explode(':', $tarif['price']);
$aTICK = explode(':', $tarif['tickrate']);

$overdue = $server['time'] < $start_point;

if ($cfg['change_slots'][$server['game']]['days'] || $overdue) {
    // Цена за 1 день
    $price = $aPrice[array_search($server['tickrate'], $aTICK)] / 30;

    // Цена аренды за остаток дней (с текущим кол-вом слот)
    $price_old = ($server['time'] - $start_point) / 86400 * $price * $server['slots'];
}

$max = $tarif['slots_max'] - $server['slots'];

// Сумма за добавляемые слоты
$sum = round(($server['time'] - $start_point) / 86400 * ($aPrice[array_search($server['tickrate'], $aTICK)] / 30) * $slots, 2);

include(SEC . 'servers/games/tarif/slots.php');
