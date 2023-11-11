<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(LIB.'games/scans.php');

	class scan extends scans
	{
		public static function mon($id, $players_get = false)
		{
			global $cfg, $sql, $html, $mcache;

			include(LIB.'games/query/Buffer.php');
			include(LIB.'games/query/BaseSocket.php');
			include(LIB.'games/query/Socket.php');
			include(LIB.'games/query/SourceQuery.php');

			$sq = new SourceQuery();

			if($players_get)
				$nmch = 'server_scan_mon_pl_'.$id;
			else
				$nmch = 'server_scan_mon_'.$id;

			if(is_array($mcache->get($nmch)))
				return $mcache->get($nmch);

			$out = array();

			$info = scan::info($sq, $id);

			$sql->query('SELECT `game`, `name`, `map`, `online`, `players`, `status`, `time`, `overdue` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$out['time'] = 'Арендован до: '.date('d.m.Y - H:i', $server['time']);

			if($server['status'] == 'overdue')
				$out['time_end'] = 'Удаление через: '.sys::date('min', $server['overdue']+$cfg['server_delete']*86400);
			else
				$out['time_end'] = 'Осталось: '.sys::date('min', $server['time']);

			if(!$info['status'])
			{
				$out['name'] = $server['name'];
				$out['status'] = sys::status($server['status'], $server['game'], $server['map']);
				$out['online'] = $server['online'];
				$out['image'] = '<img src="'.sys::status($server['status'], $server['game'], $server['map'], 'img').'">';
				$out['buttons'] = sys::buttons($id, $server['status']);

				if($players_get)
					$out['players'] = base64_decode($server['players']);

				$mcache->set($nmch, $out, false, $cfg['mcache_server_mon']);

				return $out;
			}

			if($players_get)
				$players = scan::info($sq, $id, true);

			$out['name'] = htmlspecialchars($info['name']);
			$out['status'] = sys::status('working', $server['game'], $info['map']);
			$out['online'] = $info['online'];
			$out['image'] = '<img src="'.sys::status('working', $server['game'], $info['map'], 'img').'">';
			$out['buttons'] = sys::buttons($id, 'working');
			$out['players'] = '';

			if($players_get)
			{
				foreach($players as $index => $player)
				{
					$html->get($server['game'], 'sections/servers/players');

						$html->set('i', $player['i']);
						$html->set('name', $player['name']);
						$html->set('score', $player['score']);
						$html->set('time', $player['time']);

					$html->pack('list');
				}

				$out['players'] = isset($html->arr['list']) ? $html->arr['list'] : '';
			}

			$sql->query('UPDATE `servers` set '
				.'`name`="'.$out['name'].'", '
				.'`online`="'.$out['online'].'", '
				.'`map`="'.$info['map'].'", '
				.'`status`="working" WHERE `id`="'.$id.'" LIMIT 1');

			if($players_get)
				$sql->query('UPDATE `servers` set `players`="'.base64_encode($out['players']).'" WHERE `id`="'.$id.'" LIMIT 1');	

			$mcache->set($nmch, $out, false, $cfg['mcache_server_mon']);

			return $out;
		}

		public static function info($sq, $id, $pl = false)
		{
			global $sql;

			$sql->query('SELECT `address` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			list($ip, $port) = explode(':', $server['address']);

			$sq->Connect($ip, $port, 1, SourceQuery::GOLDSOURCE);

			if($pl)
			{
				$players = $sq->GetPlayers();

				$i = 1;
				$data = array();

				foreach($players as $n => $player)
				{
					$data[$i]['i'] = $i;
					$data[$i]['name'] = $player['Name'] == '' ? 'Подключается' : $player['Name'];
					$data[$i]['score'] = $player['Frags'];
					$data[$i]['time'] = $player['TimeF'];

					$i+=1;
				}

				return $data;
			}

			$data = $sq->GetInfo();

			$server['name'] = $data['HostName'];
			$server['map'] = $data['Map'];
			$server['online'] = $data['Players'];
			$server['status'] = strlen($server['map']) > 3 ? true : false;

			return $server;
		}
	}
?>