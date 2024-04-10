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
    'off' => 'Игровой сервер выключен.',
    'nomap' => 'Отсутствует стартовая карта: [map].',
    'change' => 'Отсутствует выбранная карта: [map].',
    'reinstall' => 'Повторная переустановка возможна через [time].',
    'update' => 'Повторное обновление возможно через [time].',
    'firewall' => 'Неверный формат адреса.',
    'bans' => 'Неверный формат переданных данных.',
);
