<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($nmch))
		$nmch = false;

	$aData = array();

	// Если не расчетный период
	if(!$cfg['settlement_period'])
	{
		$aData['time'] = isset($_POST['time']) ? sys::int($_POST['time']) : sys::outjs(array('e' => 'Переданы не все данные'), $nmch);

		// Проверка периода
		if(!in_array($aData['time'], explode(':', $tarif['timext'])))
			sys::outjs(array('e' => 'Переданы неверные данные'), $nmch);

	}

	$aData['promo'] = isset($_POST['promo']) ? $_POST['promo'] : '';
	$aData['address'] = isset($_POST['address']) ? $_POST['address'] : false;
	$aData['server'] = $id;
	$aData['user'] = $server['user'];
	$aData['tarif'] = $server['tarif'];
	$aData['fps'] = $server['fps'];
	$aData['tickrate'] = $server['tickrate'];
	$aData['slots'] = $server['slots'];

	// Цена за выделенный адрес
	$add_sum = tarifs::address_add_sum($aData['address'], $server);

	$aPrice = sys::b64djs($tarif['price']);

	// Цена за 30 дней 1 слота
	$price = $aPrice[$server['tickrate'].'_'.$server['fps']];

	// Если расчетный период
	if($cfg['settlement_period'])
		$aData['time'] = $server['time'];

	// Цена аренды
	$sum = games::define_sum($tarif['discount'], $price, $server['slots'], $aData['time'], 'extend')+$add_sum;

	// Если расчетный период
	if($cfg['settlement_period'])
		$aData['time'] = games::define_period('extend', params::$aDayMonth, $server['time']);

	$days = params::$aDayMonth[date('n', $server['time'])] == $aData['time'] ? 'месяц' : games::parse_day($aData['time'], true);

	include(SEC.'servers/games/tarif/extend.php');
?>