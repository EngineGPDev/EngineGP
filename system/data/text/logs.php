<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$text = array(
    'buy_server' => 'Аренда игрового сервера на срок: [days], списана сумма: [money] руб. (сервер: #[id])',
    'buy_control' => 'Аренда услуги \"Контроль\" на срок: [days], списана сумма: [money] руб. (сервер: #[id])',
    'buy_server_promo' => 'Аренда игрового сервера на срок: [days], использование промо-кода: [promo], списана сумма: [money] руб. (сервер: #[id])',
    'buy_server_test' => 'Получение тестового периода для игрового сервера, списана сумма: 0 руб. (сервер: #[id])',
    'extend_server' => 'Продление игрового сервера на срок: [days], списана сумма: [money] руб. (сервер: #[id])',
    'extend_control' => 'Продление подключенного сервера на срок: [days], списана сумма: [money] руб. (контроль: #[id])',
    'extend_server_promo' => 'Продление игрового сервера на срок: [days], использование промо-кода: [promo], списана сумма: [money] руб. (сервер: #[id])',
    'part' => 'Партнерская программа: прибыль [money] руб. от партнера (пользоветль: #[part])',
    'cashback' => 'Вывод средств на [purse], сумма [money] руб.',
    'profit' => 'Продажа привилегии на игровом сервере: прибыль [money] руб. (сервер: #[server])',
    'buy_address' => 'Аренда выделенного адреса, списана сумма: [money] руб. (сервер: #[id])',
    'extend_address' => 'Продление аренды выделенного адреса, списана сумма: [money] руб. (сервер: #[id])',
    'buy_slots' => 'Аренда дополнительных слот: [slots] шт., списана сумма: [money] руб. (сервер: #[id])',
    'buy_boost' => 'Покупка кругов: [circles] шт. на сайте: [site], списана сумма: [money] руб. (сервер: #[id])',
    'buy_plugin' => 'Покупка плагина: [plugin], списана сумма: [money] руб. (сервер: #[id])',
    'ctrl_buy_plugin' => 'Покупка плагина: [plugin], списана сумма: [money] руб. (CTRL сервер: #[id])',
);
