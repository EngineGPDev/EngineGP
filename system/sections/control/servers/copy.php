<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `address`, `game`, `pack`, `status` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
	$server = $sql->get();

	ctrl::nav($server, $id, $sid, 'copy');

	include(ctrl::route($server, 'copy', $go));
?>