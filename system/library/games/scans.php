<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scans
	{
		private static $process = array(
			'cs' => 'hlds_',
			'cssold' => 'srcds_i686',
			'css' => 'srcds_',
			'csgo' => 'srcds_',
			'samp' => 'samp',
			'crmp' => 'samp',
			'mta' => 'mta',
			'mc' => 'java'
		);

		public static function resources($id)
		{
			global $cfg, $sql, $mcache;

			$nmch = 'server_resources_'.$id;

			if(is_array($mcache->get($nmch)))
				return $mcache->get($nmch);

			$sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `slots`, `slots_start`, `status`, `online`, `ram`, `hdd`, `hdd_use` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');

			if(!$sql->num())
				return NULL;

			$server = $sql->get();

			$resources = array(
				'usr' => 0,
				'cpu' => 0,
				'ram' => 0,
				'hdd' => $server['hdd_use']
			);

			$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
			$tarif = $sql->get();

			$sql->query('SELECT `address`, `passwd`, `ram` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return $resources;

			if(!in_array($server['status'], array('working', 'start', 'restart', 'change')))
				return $resources;

			$resources['usr'] = ceil(100/$server['slots_start']*$server['online']);
			$resources['usr'] = $resources['usr'] > 100 ? 100 : $resources['usr'];
 
			$cr = explode('|', $ssh->get('top -u '.$server['uid'].' -b -n 1 | grep '.(scans::$process[$server['game']]).' | sort | tail -1 | awk \'{print $9"|"$10}\''));

			if(isset($cr[0]))
				$resources['cpu'] = str_replace(',', '.', $cr[0]);

			$resources['cpu'] = $resources['cpu'] > 100 ? 100 : round($resources['cpu']);

			if(isset($cr[1]))
				$resources['ram'] = str_replace(',', '.', $cr[1]);

			// ram на сервер
			$ram = $server['ram'] ? $server['ram'] : $server['slots']*$cfg['ram'][$server['game']];

			$resources['ram'] = $unit['ram']/100*$resources['ram']/($ram/100);
	
			$resources['ram'] = $resources['ram'] > 100 ? 100 : round($resources['ram']);

			$resources['hdd'] = ceil(sys::int($ssh->get('cd '.$tarif['install'].$server['uid'].' && du -ms'))/($server['hdd']/100));
			$resources['hdd'] = $resources['hdd'] > 100 ? 100 : $resources['hdd'];

			$sql->query('UPDATE `servers` set `ram_use`="'.$resources['ram'].'", `cpu_use`="'.$resources['cpu'].'", `hdd_use`="'.$resources['hdd'].'" WHERE `id`="'.$id.'" LIMIT 1');

			$mcache->set($nmch, $resources, false, $cfg['mcache_server_resources']);

			return $resources;
		}

		public static function status($id)
		{
			global $start_point, $cfg, $sql, $mcache;

			$nmch = 'server_status_'.$id;

			if($mcache->get($nmch))
				return 'mcache -> system_block_operation';

			$mcache->set($nmch, true, false, $cfg['mcache_server_status']);

			$sql->query('SELECT `uid`, `unit`, `game`, `address`, `status`, `name`, `online`, `players`, `time`, `overdue`, `ftp`, `block` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			// Если аренда не закончилась, а сервер просрочен
			if($server['time'] > $start_point && $server['status'] == 'overdue')
			{
				$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

				sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
				sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

				return 'server -> extend -> off';
			}

			// Если аренда закончилась и сервер просрочен длительное время
			if($server['time'] < $start_point && $server['status'] == 'overdue' && ($server['overdue']+$cfg['server_delete']*86400) < $start_point)
			{
				$sql->query('UPDATE `servers` set `user`="-1" WHERE `id`="'.$id.'" LIMIT 1');

				return 'server -> overdue -> delete';
			}

			$sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return 'unit error connect';

			// Если аренда закончилась, а сервер не просрочен
			if($server['time'] < $start_point && !in_array($server['status'], array('overdue', 'blocked')))
			{
				// Убить процессы
				$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
						.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

				if($server['ftp'])
					$ssh->set("mysql -P ".$unit['sql_port']." -u".$unit['sql_login']." -p".$unit['sql_passwd']." --database ".$unit['sql_ftp']." -e \"DELETE FROM ftp WHERE user='".$server['uid']."'\"");

				$sql->query('UPDATE `servers` set `status`="overdue", `online`="0", `players`="", `ftp`="0", `overdue`="'.$start_point.'", `mail`="1" WHERE `id`="'.$id.'" LIMIT 1');

				sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'overdue', 'online' => 0, 'players' => ''));
				sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'overdue', 'online' => 0));

				return 'server -> overdue -> stoping';
			}

			switch($server['status'])
			{
				case 'working': case 'change': case 'start': case 'restart':
					if(!sys::int($ssh->get('ps aux | grep s_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `servers` set `status`="off", `online`="0", `players`="0" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> working -> off';
					}

					break;

				case 'off':
					if(sys::int($ssh->get('ps aux | grep s_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{	
						$sql->query('UPDATE `servers` set `status`="working" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'working', 'online' => $server['online'], 'players' => $server['players']));
						sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'working', 'online' => $server['online']));

						return 'server -> off -> working';
					}

					break;

				case 'reinstall':
					if(!sys::int($ssh->get('ps aux | grep r_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> reinstall -> end';
					}

					break;

				case 'update':
					if(!sys::int($ssh->get('ps aux | grep u_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> update -> end';
					}

					break;

				case 'install':
					if(!sys::int($ssh->get('ps aux | grep i_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> install -> end';
					}

					break;

				case 'recovery':
					if(!sys::int($ssh->get('ps aux | grep rec_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> recovery -> end';
					}

					break;

				case 'blocked':
					if($server['block'] > $start_point)
						break;

					$sql->query('UPDATE `servers` set `status`="off", `block`="0" WHERE `id`="'.$id.'" LIMIT 1');

					sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
					sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));
			}

			return 'server -> no change -> end scan';
		}
	}
?>