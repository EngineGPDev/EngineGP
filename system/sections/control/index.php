<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Список игровых серверов #'.$id);

	$sql->query('SELECT '
		.'`id`,'
		.'`address`,'
		.'`game`,'
		.'`slots`,'
		.'`online`,'
		.'`status`,'
		.'`name`,'
		.'`map`'
		.' FROM `control_servers` WHERE `unit`="'.$id.'" ORDER BY `id` ASC');

	$wait_servers = '';
	$updates_servers = '';

	while($server = $sql->get())
	{
		$btn = sys::buttons($server['id'], $server['status'], $server['game'], $id);

		$html->get('list', 'sections/control/servers');

			$html->set('ctrl', $id);
			$html->set('id', $server['id']);
			$html->set('address', $server['address']);
			$html->set('game', $aGname[$server['game']]);
			$html->set('slots', $server['slots']);
			$html->set('online', $server['online']);
			$html->set('name', $server['name']);
			$html->set('status', sys::status($server['status'], $server['game'], $server['map']));
			$html->set('img', sys::status($server['status'], $server['game'], $server['map'], 'img', $server['game']));
			$html->set('btn', $btn);

		$html->pack('list');

		$wait_servers .= $server['id'].':false,';
		$updates_servers .= 'setTimeout(function() {update_info(\''.$server['id'].'\', \''.$id.'\', true)}, 5000);'
			.'setTimeout(function() {update_status(\''.$server['id'].'\', \''.$id.'\', true)}, 5000);'
			.'setTimeout(function() {update_resources(\''.$server['id'].'\', \''.$id.'\', true)}, 3000);';
	}

	$html->get('servers', 'sections/control/servers');

		$html->set('list', isset($html->arr['list']) ? $html->arr['list'] : 'Нет установленных серверов');
		$html->set('wait_servers', $wait_servers);
		$html->set('updates_servers', $updates_servers);

	$html->pack('main');
?>