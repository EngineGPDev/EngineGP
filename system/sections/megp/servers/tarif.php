<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `unit`, `user`, `tarif`, `address`, `port`, `game`, `status`, `slots`, `slots_start`, `plugins_use`, `ftp_use`, `console_use`, `stats_use`, `copy_use`, `web_use`, `time`, `test`, `fps`, `tickrate`, `ram`, `ram_fix` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = $sql->get();

	sys::nav($server, $id, 'tarif');

	if($server['status'] == 'blocked')
	{
		if($go)
			sys::out('Раздел недоступен');

		include(SEC.'megp/servers/noaccess.php');
	}else
		include(SEC.'megp/servers/'.$server['game'].'/tarif.php');
?>