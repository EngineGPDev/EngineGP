<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scan_servers_down extends cron
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

			if(!array_key_exists($game, cron::$quakestat))
				return NULL;

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
				$sql->query('SELECT `uid`, `address`, `status`, `autorestart` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
				$server = $sql->get();

				if(!$server['autorestart'])
					continue;

				if($server['status'] != 'working')
					continue;

				if(!in_array(trim($ssh->get('quakestat -'.(cron::$quakestat[$game]).' '.$server['address'].' -retry 5 -interval 2 | grep -v frags | tail -1 | awk \'{print $2}\'')), array('DOWN', 'no')))
					continue;

				exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action restart '.$game.' '.$id.'"');

				$sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="'.$id.'", `text`="Перезагрука сервера: сервер завис", `time`="'.$start_point.'"');
			}

			return NULL;
		}
	}
?>