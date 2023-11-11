<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aPacks = $cfg['control_packs'][$server['game']];

	$pack = isset($url['pack']) ? $url['pack'] : exit;

	if($pack == $server['pack'])
		sys::outjs(array('s' => 'ok'));

	// Проверка сборки
	if(!array_key_exists($pack, $aPacks))
		sys::outjs(array('e' => 'Сборка не найдена.'));

	$sql->query('UPDATE `control_servers` set `pack`="'.$pack.'" WHERE `id`="'.$sid.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), 'ctrl_server_settings_'.$sid);
?>