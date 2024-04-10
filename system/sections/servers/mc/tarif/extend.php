<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

if (!isset($nmch))
    $nmch = false;

$aData = array();

// Если не расчетный период
if (!$cfg['settlement_period']) {
    $aData['time'] = isset($_POST['time']) ? sys::int($_POST['time']) : sys::outjs(array('e' => 'Переданы не все данные'), $nmch);

    // Проверка периода
    if (!in_array($aData['time'], explode(':', $tarif['timext'])))
        sys::outjs(array('e' => 'Переданы неверные данные'), $nmch);

}

$ram = $server['slots_fix'] ? $server['ram'] : $server['ram'] / $server['slots'];

$aData['promo'] = isset($_POST['promo']) ? $_POST['promo'] : '';
$aData['address'] = isset($_POST['address']) ? $_POST['address'] : false;
$aData['server'] = $id;
$aData['user'] = $server['user'];
$aData['tarif'] = $server['tarif'];
$aData['ram'] = $ram;
$aData['slots'] = $server['slots'];

// Цена за выделенный адрес
$add_sum = tarifs::address_add_sum($aData['address'], $server);

$aPrice = explode(':', $tarif['price']);

// Цена за 30 дней 1 слота
$price = $aPrice[array_search($ram, explode(':', $tarif['ram']))];

// Если расчетный период
if ($cfg['settlement_period'])
    $aData['time'] = $server['time'];

// Цена аренды
$sum = games::define_sum($tarif['discount'], $price, $server['slots'], $aData['time'], 'extend') + $add_sum;

// Если расчетный период
if ($cfg['settlement_period'])
    $aData['time'] = games::define_period('extend', params::$aDayMonth, $server['time']);

$days = params::$aDayMonth[date('n', $server['time'])] == $aData['time'] ? 'месяц' : games::parse_day($aData['time'], true);

include(SEC . 'servers/games/tarif/extend.php');
