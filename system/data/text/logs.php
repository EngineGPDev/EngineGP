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

$text = [
    'buy_server' => 'Аренда игрового сервера на срок: [days], списана сумма: [money] руб. (сервер: #[id])',
    'buy_server_promo' => 'Аренда игрового сервера на срок: [days], использование промо-кода: [promo], списана сумма: [money] руб. (сервер: #[id])',
    'buy_server_test' => 'Получение тестового периода для игрового сервера, списана сумма: 0 руб. (сервер: #[id])',
    'extend_server' => 'Продление игрового сервера на срок: [days], списана сумма: [money] руб. (сервер: #[id])',
    'extend_server_promo' => 'Продление игрового сервера на срок: [days], использование промо-кода: [promo], списана сумма: [money] руб. (сервер: #[id])',
    'part' => 'Партнерская программа: прибыль [money] руб. от партнера (реферал: #[part])',
    'cashback' => 'Вывод средств на [purse], сумма [money] руб.',
    'profit' => 'Продажа привилегии на игровом сервере: прибыль [money] руб. (сервер: #[server])',
    'buy_address' => 'Аренда выделенного адреса, списана сумма: [money] руб. (сервер: #[id])',
    'extend_address' => 'Продление аренды выделенного адреса, списана сумма: [money] руб. (сервер: #[id])',
    'buy_slots' => 'Аренда дополнительных слот: [slots] шт., списана сумма: [money] руб. (сервер: #[id])',
    'buy_boost' => 'Покупка кругов: [circles] шт. на сайте: [site], списана сумма: [money] руб. (сервер: #[id])',
    'buy_plugin' => 'Покупка плагина: [plugin], списана сумма: [money] руб. (сервер: #[id])',
    'ctrl_buy_plugin' => 'Покупка плагина: [plugin], списана сумма: [money] руб. (CTRL сервер: #[id])',
];
