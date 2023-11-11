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

			$nmch = 'ctrl_server_resources_'.$id;

			if(is_array($mcache->get($nmch)))
				return $mcache->get($nmch);

			$sql->query('SELECT `uid`, `unit`, `game`, `slots`, `status`, `online`, `hdd_use` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');

			if(!$sql->num())
				return NULL;

			$server = $sql->get();

			$resources = array(
				'usr' => 0,
				'cpu' => 0,
				'ram' => 0,
				'hdd' => $server['hdd_use']
			);

			$sql->query('SELECT `address`, `passwd`, `ram`, `hdd` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return $resources;

			if(!in_array($server['status'], array('working', 'start', 'restart', 'change')))
				return $resources;

			$resources['usr'] = ceil(100/$server['slots']*$server['online']);
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

			$resources['hdd'] = ceil(sys::int($ssh->get('cd /servers/'.$server['uid'].' && du -ms'))/($unit['hdd']/100));
			$resources['hdd'] = $resources['hdd'] > 100 ? 100 : $resources['hdd'];

			$sql->query('UPDATE `control_servers` set `ram_use`="'.$resources['ram'].'", `cpu_use`="'.$resources['cpu'].'", `hdd_use`="'.$resources['hdd'].'" WHERE `id`="'.$id.'" LIMIT 1');

			$mcache->set($nmch, $resources, false, $cfg['mcache_server_resources']);

			return $resources;
		}

		public static function status($id)
		{
			global $start_point, $cfg, $sql, $mcache;

			$nmch = 'ctrl_server_status_'.$id;

			if($mcache->get($nmch))
				return 'mcache -> system_block_operation';

			$mcache->set($nmch, true, false, $cfg['mcache_server_status']);

			$sql->query('SELECT `uid`, `unit`, `game`, `address`, `status`, `name`, `online`, `players` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return 'unit error connect';

			switch($server['status'])
			{
				case 'working': case 'change': case 'start': case 'restart':
					if(!sys::int($ssh->get('ps aux | grep s_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `control_servers` set `status`="off", `online`="0", `players`="0" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> working -> off';
					}

					break;

				case 'off':
					if(sys::int($ssh->get('ps aux | grep s_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{	
						$sql->query('UPDATE `control_servers` set `status`="working" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'working', 'online' => $server['online'], 'players' => $server['players']));
						sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'working', 'online' => $server['online']));

						return 'server -> off -> working';
					}

					break;

				case 'reinstall':
					if(!sys::int($ssh->get('ps aux | grep r_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> reinstall -> end';
					}

					break;

				case 'update':
					if(!sys::int($ssh->get('ps aux | grep u_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> update -> end';
					}

					break;

				case 'install':
					if(!sys::int($ssh->get('ps aux | grep i_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> install -> end';
					}

					break;

				case 'recovery':
					if(!sys::int($ssh->get('ps aux | grep rec_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
					{
						$sql->query('UPDATE `control_servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
						sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

						return 'server -> recovery -> end';
					}
			}

			return 'server -> no change -> end scan';
		}
	}
?>