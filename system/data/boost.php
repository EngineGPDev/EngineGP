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

$aBoost = array(
    'cs' => array(
        'boost' => array(),

        'mon' => array(
            'site' => '', // адрес сайта раскрутки
            'api' => '', // адрес API сайта раскрутки
            'key' => '', // секретный ключ для API
            'services' => array(), // array(номер услуги) - выборка (номер услуги либо кол-во кругов)
            'circles' => array(), // array(номер услуги => кол-во кругов) - выборка
            'price' => array(), // array(номер услуги => цена)
            'type' => 'def' // тип работы с API сайта раскрутки
        )
    )
);
