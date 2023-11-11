<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `slots`, `online`, `players`, `name`, `pack`, `map` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
	$server = array_merge($server, $sql->get());

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Список игровых серверов #'.$id, $cfg['http'].'control/id/'.$id);
	$html->nav($server['address']);

	$btn = sys::buttons($sid, $server['status'], $server['game'], $id);

	$html->get('index', 'sections/control/servers/'.$server['game']);

		$html->set('id', $id);
		$html->set('server', $sid);
		$html->set('address', $server['address']);
		$html->set('game', $aGname[$server['game']]);
		$html->set('slots', $server['slots']);
		$html->set('online', $server['online']);
		$html->set('players', base64_decode($server['players']));
		$html->set('name', $server['name']);
		$html->set('status', sys::status($server['status'], $server['game'], $server['map']));
		$html->set('img', sys::status($server['status'], $server['game'], $server['map'], 'img'));

		$html->set('btn', $btn);

	$html->pack('main');
?>