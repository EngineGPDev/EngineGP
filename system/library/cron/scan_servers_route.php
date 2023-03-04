<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scan_servers_route extends cron
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

			$first = $ssh->get('cat /proc/stat');

			sleep(1);

			$aCpu = sys::cpu_idle(array($first, $ssh->get('cat /proc/stat')), false, true);

			array_shift($aCpu);

			$idle = array();
			$uses = array();

			foreach($aCpu as $cpu => $data)
			{
				$core = sys::int($cpu)+1;

				$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$server['unit'].'" AND `core_fix`="'.$core.'" AND `core_fix`="1" LIMIT 1');
				if($sql->num())
				{
					unset($aCpu[$cpu]);

					continue;
				}

				if($data['idle'] > 50)
					$idle[$core] = $data['idle'];
				else
					$uses[$core] = 100-$data['idle'];
			}

			if(!count($idle))
				return NULL;

			foreach($uses as $use_core => $use)
			{
				if(!count($idle))
					break;

				$sql->query('SELECT `id`, `uid` FROM `servers` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$game.'" AND `core_use`="'.$use_core.'" AND `status`="working" AND `core_fix`="0" ORDER BY `slots_start` DESC, `online` DESC LIMIT 3');
				if($sql->num() > 1)
				{
					$server = $sql->get();

					$core = array_search(max($idle), $idle);

					$aPid = explode("\n", $ssh->get('ps aux | grep -v grep | grep '.$server['uid'].' | awk \'{print $2}\''));

					if(count($aPid) < 2)
						continue;

					array_pop($aPid);

					$taskset = '';

					foreach($aPid as $pid)
						$taskset .= 'taskset -cp '.($core-1).' '.$pid.';';

					$ssh->set($taskset);

					unset($idle[$core]);

					$sql->query('UPDATE `servers` set `core_use`="'.$core.'" WHERE `id`="'.$server['id'].'" LIMIT 1');
				}
			}

			return NULL;
		}
	}
?>