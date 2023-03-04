<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
		include(SEC.'servers/'.$server['game'].'/console.php');

	$sql->query('SELECT `uid`, `unit`, `tarif`, `time_start` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = array_merge($server, $sql->get());

	$html->get('console', 'sections/servers/'.$server['game']);
		$html->set('id', $id);
	$html->pack('main');
?>