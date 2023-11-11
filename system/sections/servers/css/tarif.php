<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `name`, `slots_min`, `slots_max`, `install`, `fps`, `tickrate`, `ram`, `timext`, `discount`, `price` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	// Подразделы
	$aSub = array('extend', 'plan', 'address', 'addextend', 'unit', 'slots');

	include(SEC.'servers/games/tarif.php');
?>