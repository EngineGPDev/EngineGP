<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$aBoost = ['cs' => ['boost' => [], 'mon' => [
    'site' => '',
    // адрес сайта раскрутки
    'api' => '',
    // адрес API сайта раскрутки
    'key' => '',
    // секретный ключ для API
    'services' => [],
    // array(номер услуги) - выборка (номер услуги либо кол-во кругов)
    'circles' => [],
    // array(номер услуги => кол-во кругов) - выборка
    'price' => [],
    // array(номер услуги => цена)
    'type' => 'def',
]]];
