<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$q_Servers = $sql->query('SELECT `unit`, `tarif` FROM `servers` WHERE `user`="'.$user['id'].'" ORDER BY `id` ASC');

	$n = $sql->num($q_Servers);

	$aUnits = array();
	$aTarifs = array();

	// Проверка массивов в кеше
	if(is_array($mcache->get('aut_'.$user['id'])) AND $mcache->get('nser_'.$user['id']) == $n)
	{
		$aUT = $mcache->get('aut_'.$user['id']);
		$aUnits = $aUT[0];
		$aTarifs = $aUT[1];
	}else{
		while($server = $sql->get($q_Servers))
		{
			if(!array_key_exists($server['unit'], $aUnits))
			{
				$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
				$unit = $sql->get();

				$aUnits[$server['unit']] = array(
					'name' => $unit['name']
				);
			}

			if(!array_key_exists($server['tarif'], $aTarifs))
			{
				$sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
				$tarif = $sql->get();

				$aTarifs[$server['tarif']] = array(
					'name' => $tarif['name'],
					'packs' => sys::b64djs($tarif['packs'])
				);
			}
		}

		// Запись массивов в кеш
		$mcache->set('aut_'.$user['id'], array($aUnits, $aTarifs), false, 60);

		// Запись кол-во серверов в кеш
		$mcache->set('nser_'.$user['id'], $n, false, 60);
	}

	include(LIB.'games/games.php');

	$sql->query('SELECT '
		.'`id`,'
		.'`unit`,'
		.'`tarif`,'
		.'`address`,'
		.'`game`,'
		.'`slots_start`,'
		.'`online`,'
		.'`status`,'
		.'`name`,'
		.'`pack`,'
		.'`fps`,'
		.'`tickrate`,'
		.'`ram`,'
		.'`map`,'
		.'`time`,'
		.'`date`,'
		.'`overdue`'
		.' FROM `servers` WHERE `user`="'.$user['id'].'" ORDER BY `id` ASC');

	$wait_servers = '';
	$updates_servers = '';

	while($server = $sql->get())
	{
		$btn = sys::buttons($server['id'], $server['status'], $server['game']);

		$time_end = $server['status'] == 'overdue' ? 'Удаление через: '.sys::date('min', $server['overdue']+$cfg['server_delete']*86400) : 'Осталось: '.sys::date('min', $server['time']);

		$html->get('list', 'sections/servers');

			$html->set('id', $server['id']);
			$html->set('unit', $aUnits[$server['unit']]['name']);
			$html->set('tarif',
				games::info_tarif(
					$server['game'],
					$aTarifs[$server['tarif']]['name'],
					array('fps' => $server['fps'], 'tickrate' => $server['tickrate'], 'ram' => $server['ram'])
				)
			);

			$html->set('pack', $aTarifs[$server['tarif']]['packs'][$server['pack']]);
			$html->set('address', $server['address']);
			$html->set('game', $aGname[$server['game']]);
			$html->set('slots', $server['slots_start']);
			$html->set('online', $server['online']);
			$html->set('name', $server['name']);
			$html->set('fps', $server['fps']);
			$html->set('status', sys::status($server['status'], $server['game'], $server['map']));
			$html->set('img', sys::status($server['status'], $server['game'], $server['map'], 'img', $server['game']));
			$html->set('time_end', $time_end);
			$html->set('time', sys::today($server['time']));
			$html->set('date', sys::today($server['date']));

			$html->set('btn', $btn);

		$html->pack('list');

		$wait_servers .= $server['id'].':false,';
		$updates_servers .= 'setTimeout(function() {update_info(\''.$server['id'].'\', true)}, 5000); setTimeout(function() {update_status(\''.$server['id'].'\', true)}, 10000);';
	}
?>