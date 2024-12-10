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
    'off' => 'Игровой сервер выключен.',
    'nomap' => 'Отсутствует стартовая карта: [map].',
    'change' => 'Отсутствует выбранная карта: [map].',
    'reinstall' => 'Повторная переустановка возможна через [time].',
    'update' => 'Повторное обновление возможно через [time].',
    'firewall' => 'Неверный формат адреса.',
    'bans' => 'Неверный формат переданных данных.',
];
