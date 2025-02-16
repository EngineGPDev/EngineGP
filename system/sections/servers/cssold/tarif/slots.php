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

if (!isset($nmch)) {
    $nmch = false;
}

// Если фикс. значение слот
if ($tarif['slots_min'] == $tarif['slots_max']) {
    System::outjs(['e' => 'На данном тарифе нельзя изменить количество слот.'], $nmch);
}

$slots = isset($url['slots']) ? System::int($url['slots']) : System::outjs(['e' => 'Переданы не все данные.'], $nmch);

$aPrice = System::b64djs($tarif['price']);

$overdue = $server['time'] < $start_point;

if ($cfg['change_slots'][$server['game']]['days'] || $overdue) {
    // Цена за 1 день
    $price = $aPrice[$server['tickrate'] . '_' . $server['fps']] / 30;

    // Цена аренды за остаток дней (с текущим кол-вом слот)
    $price_old = ($server['time'] - $start_point) / 86400 * $price * $server['slots'];
}

$max = $tarif['slots_max'] - $server['slots'];

// Сумма за добавляемые слоты
$sum = round(($server['time'] - $start_point) / 86400 * ($aPrice[$server['tickrate'] . '_' . $server['fps']] / 30) * $slots, 2);

include(SEC . 'servers/games/tarif/slots.php');
