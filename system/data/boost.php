<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    $aBoost = array(
		'cs' => array(
			'boost' => array(
				
			),

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
?>