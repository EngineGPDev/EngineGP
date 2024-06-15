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

if (!defined('EGP'))
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
