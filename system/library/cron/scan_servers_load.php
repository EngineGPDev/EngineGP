<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scan_servers_load extends cron
	{
		function __construct()
		{
			global $cfg, $sql, $argv, $start_point;

			$servers = $argv;

			unset($servers[0], $servers[1], $servers[2]);

			$sql->query('SELECT `address` FROM `units` WHERE `id`="'.$servers[4].'" LIMIT 1');
			if(!$sql->num())
				return NULL;

			$unit = $sql->get();

			$game = $servers[3];

			unset($servers[3], $servers[4]);

			$sql->query('SELECT `unit` FROM `servers` WHERE `id`="'.$servers[5].'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd`, `ram` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return NULL;

			foreach($servers as $id)
			{
				$sql->query('SELECT `uid`, `slots`, `hdd`, `ram`, `ram_use_max`, `cpu_use_max`, `core_fix`, `core_fix_one` `status` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
				$server = $sql->get();

				if($server['core_fix'] AND $server['core_fix_one'])
					continue;

				if(!in_array($server['status'], array('working', 'start', 'restart', 'change')))
				{
					echo 'server#'.$id.' ('.$game.') -> load average: cpu = 0% / ram = 0% (no working)'.PHP_EOL;

					continue;
				}

				$resources = array();

				for($n = 0; $n <= 2; $n+=1)
				{
					$cr = explode('|', $ssh->get('top -u '.$server['uid'].' -b -n 1 | grep '.(cron::$process[$game]).' | awk \'{print $9"|"$10}\''));

					$resources[$n]['cpu'] = isset($cr[0]) ? round(str_replace(',', '.', $cr[0])) : 0;

					$resources[$n]['ram'] = isset($cr[1]) ? str_replace(',', '.', $cr[1]) : 0;
					$ram = $server['ram'] ? $server['ram'] : $server['slots']*$cfg['ram'][$game];
					$resources[$n]['ram'] = round($unit['ram']/100*$resources[$n]['ram']/($ram/100));

					sleep(1);
				}

				$loads = array();

				foreach($resources as $n => $load)
				{
					foreach($load as $type => $val)
						$loads[$type] += $val;
				}

				$average_cpu = isset($loads['cpu']) ? $loads['cpu']/2 : 0;
				$average_ram = isset($loads['ram']) ? $loads['ram']/2 : 0;

				$max_cpu = $server['cpu_use_max'] ? $server['cpu_use_max'] : $cfg['cpu_use_max'][$game];
				$max_ram = $server['ram_use_max'] ? $server['ram_use_max'] : $cfg['ram_use_max'][$game];

				if($average_cpu > $max_cpu)
				{
					exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action restart '.$game.' '.$id.'"');

					$sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="'.$id.'", `text`="Перезагрука сервера: OVERLOAD^cpu = '.$average_cpu.'%", `time`="'.$start_point.'"');
				}elseif($average_ram > $max_ram){
					exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action restart '.$game.' '.$id.'"');

					$sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="'.$id.'", `text`="Перезагрука сервера: OVERLOAD^ram = '.$average_ram.'%", `time`="'.$start_point.'"');
				}
			}

			return NULL;
		}
	}
?>