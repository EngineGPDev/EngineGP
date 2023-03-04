<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class api
	{
		public function data($id)
		{
			global $sql, $cfg;

			$sql->query('SELECT `unit`, `tarif`, `address`, `game`, `slots_start`, `online`, `players`, `status`, `name`, `map`, `pack`, `fps`, `tickrate`, `ram`, `time`, `date`, `overdue` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			if(!$sql->num())
				return array('e' => 'сервер не найден');

			$server = $sql->get();

			$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			if(!$sql->num())
				return array('e' => 'локация не найдена');

			$unit = $sql->get();
			
			$sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
			if(!$sql->num())
				return array('e' => 'тариф не найден');

			$tarif = $sql->get();
			$packs = sys::b64djs($tarif['packs']);

			$time_end = $server['status'] == 'overdue' ? 'Удаление через: '.sys::date('min', $server['overdue']+$cfg['server_delete']*86400) : 'Осталось: '.sys::date('min', $server['time']);

			return array(
				'id' => $id,
				'address' => $server['address'],
				'unit' => $unit['name'],
				'tarif' => games::info_tarif($server['game'], $tarif['name'], array('fps' => $server['fps'], 'tickrate' => $server['tickrate'], 'ram' => $server['ram'])),
				'game' => $server['game'],
				'name' => $server['name'],
				'slots' => $server['slots_start'],
				'online' => $server['online'],
				'players' => $server['players'],
				'status' => sys::status($server['status'], $server['game'], $server['map']),
				'img' => sys::status($server['status'], $server['game'], $server['map'], 'img'),
				'time_end' => $time_end,
				'time' => sys::today($server['time']),
				'date' => sys::today($server['date']),
				'pack' => $packs[$server['pack']]
			);
		}

		public function load($id)
		{
			global $sql, $cfg;

			$sql->query('SELECT `online`, `slots_start`, `ram_use`, `cpu_use`, `hdd_use` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			if(!$sql->num())
				return array('e' => 'сервер не найден');

			$server = $sql->get();

			$online = 100/$server['slots_start']*$server['online'];
			$online = $online > 100 ? 100: $online;

			return array(
				'id' => $id,
				'cpu' => $server['cpu_use'],
				'ram' => $server['ram_use'],
				'hdd' => $server['hdd_use'],
				'onl' => $online
			);
		}

		public function console($id, $cmd)
		{
			global $sql, $cfg;

			$aGames = array('cs', 'css', 'cssold', 'csgo', 'mc', 'mta');

			$sql->query('SELECT `game` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			if(!$sql->num())
				return 'сервер не найден';

			$server = $sql->get();

			if(!in_array($server['game'], $aGames))
				return 'Игра не поддерживает команды';

			$go = true;

			$_POST['command'] = isset($cmd{0}) ? urldecode($cmd) : '';

			include(SEC.'servers/'.$server['game'].'/console.php');
		}
	}
?>