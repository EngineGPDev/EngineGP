<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($nmch))
		$nmch = false;

	$uid = isset($url['uid']) ? sys::int($url['uid']) : sys::outjs(array('e' => 'Переданы не все данные.'), $nmch);

	if(!$cfg['change_unit'][$server['game']] || $server['time'] < $start_point+86400 || $server['test'])
		exit;

	$sql->query('SELECT `id`, `unit`, `packs`, `fps`, `tickrate`, `price` FROM `tarifs` WHERE `unit`="'.$uid.'" AND `game`="'.$server['game'].'" AND `name`="'.$tarif['name'].'" AND `id`!="'.$server['tarif'].'" AND `show`="1" ORDER BY `unit`');
	if(!$sql->num())
		sys::outjs(array('e' => 'Не найден подходящий тариф.'), $nmch);

	$oldTarif = $tarif;

	$tarif = $sql->get();

	$sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$oldUnit = $sql->get();

	$aPriceold = sys::b64djs($oldTarif['price']);

	$sql->query('SELECT `id` FROM `units` WHERE `id`="'.$tarif['unit'].'" AND `show`="1" LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('e' => 'Выбранная локация не доступна.'), $nmch);

	$aPrice = sys::b64djs($tarif['price']);

	if(!array_key_exists($server['tickrate'].'_'.$server['fps'], $aPrice))
		sys::outjs(array('e' => 'Не найден подходящий тарифный план.'), $nmch);

	// Цена за 1 день (при новом тарифном плане)
	$price = $aPrice[$server['tickrate'].'_'.$server['fps']]/30*$server['slots'];

	// Цена аренды за остаток дней (с текущим тарифным планом)
	$oldprice = ($server['time']-$start_point)/86400*($aPriceold[$server['tickrate'].'_'.$server['fps']]/30*$server['slots']);

	$date = date('H.i.s.d.m.Y', round($start_point+$oldprice/$price*86400-86400));

	$aDate = explode('.', $date);

	$time = mktime($aDate[0], $aDate[1], $aDate[2], $aDate[4], $aDate[3], $aDate[5]);

	include(SEC.'servers/games/tarif/unit.php');
?>