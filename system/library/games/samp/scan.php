<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(LIB.'games/scans.php');

	class scan extends scans
	{
		public static function mon($id, $players_get = false)
		{
			global $cfg, $sql, $html, $mcache;

			$sql->query('SELECT `address`, `game`, `name`, `map`, `online`, `players`, `status`, `time`, `overdue` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			list($ip, $port) = explode(':', $server['address']);

			include(LIB.'games/query/SampQuery.php');

			$sq = new SampQuery($ip, $port);

			if($players_get)
				$nmch = 'server_scan_mon_pl_'.$id;
			else
				$nmch = 'server_scan_mon_'.$id;

			if(is_array($mcache->get($nmch)))
				return $mcache->get($nmch);

			$out = array();

			$out['time'] = 'Арендован до: '.date('d.m.Y - H:i', $server['time']);

			if($server['status'] == 'overdue')
				$out['time_end'] = 'Удаление через: '.sys::date('min', $server['overdue']+$cfg['server_delete']*86400);
			else
				$out['time_end'] = 'Осталось: '.sys::date('min', $server['time']);

			if(!$sq->connect())
			{
				$out['name'] = $server['name'];
				$out['status'] = sys::status($server['status'], $server['game'], $server['map']);
				$out['online'] = $server['online'];
				$out['image'] = '<img src="'.sys::status($server['status'], $server['game'], $server['map'], 'img').'">';
				$out['buttons'] = sys::buttons($id, $server['status'], $server['game']);

				if($players_get)
					$out['players'] = base64_decode($server['players']);

				$mcache->set($nmch, $out, false, $cfg['mcache_server_mon']);

				return $out;
			}

			$info = $sq->getInfo();

			if($players_get)
				$players = scan::players($sq->getDetailedPlayers());

			$info['map'] = htmlspecialchars(mb_convert_encoding($info['map'], 'UTF-8', 'WINDOWS-1251'));
			$out['name'] = htmlspecialchars(mb_convert_encoding($info['hostname'], 'UTF-8', 'WINDOWS-1251'));
			$out['status'] = sys::status('working', $server['game'], $info['map']);
			$out['online'] = sys::int($info['players']);
			$out['image'] = '<img src="'.sys::status('working', $server['game'], 'samp', 'img').'">';
			$out['buttons'] = sys::buttons($id, 'working', $server['game']);
			$out['players'] = '';

			if($players_get)
			{
				foreach($players as $index => $player)
				{
					$html->get($server['game'], 'sections/servers/players');

						$html->set('i', $player['i']);
						$html->set('name', htmlspecialchars($player['name']));

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

		public static function players($aPlayrs)
		{
			$i = 1;
			$aData = array();

			foreach($aPlayrs as $n => $player)
			{
				$aData[$i]['i'] = $i;
				$aData[$i]['name'] = $player['nickname'] == '' ? 'Подключается' : htmlspecialchars($player['nickname']);
				$aData[$i]['ping'] = sys::int($player['ping']);

				$i+=1;
			}

			return $aData;
		}
	}
?>