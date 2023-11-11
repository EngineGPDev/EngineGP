<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `game` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = $sql->get();

	include(LIB.'games/'.$server['game'].'/scan.php');

	// Запрошена информация (статус, онлайн, название)
	if(isset($url['mon']))
		sys::outjs(scan::mon($id));

	// Запрошена информация (статус, онлайн, название, игроки)
	if(isset($url['fmon']))
		sys::outjs(scan::mon($id, true));

	// Запрошена информация (cpu, ram, hdd)
	if(isset($url['resources']))
		sys::outjs(scan::resources($id));

	// Запрошена информация (работает, меняется карта, переустанавливается)
	if(isset($url['status']))
		sys::outjs(scan::status($id));

	exit;
?>