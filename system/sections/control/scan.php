<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Запрошена информация (cpu, ram, hdd)
	if(isset($url['resources']))
		sys::outjs(ctrl::resources($id));

	// Запрошена подробная информация (cpu, ram, hdd)
	if(isset($url['update_info']))
		sys::outjs(ctrl::update_info($id));

	// Обновление информации (status, time)
	if(isset($url['update_status']))
		sys::outjs(ctrl::update_status($id));

	exit;
?>