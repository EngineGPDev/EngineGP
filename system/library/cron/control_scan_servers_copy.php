<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class control_scan_servers_copy extends cron
	{
		function __construct()
		{
			global $sql, $argv, $start_point;

			$servers = $argv;

			unset($servers[0], $servers[1], $servers[2]);

			$sql->query('SELECT `address` FROM `control` WHERE `id`="'.$servers[4].'" LIMIT 1');
			if(!$sql->num())
				return NULL;

			$unit = $sql->get();

			$game = $servers[3];

			unset($servers[3], $servers[4]);

			$sql->query('SELECT `unit` FROM `control_servers` WHERE `id`="'.$servers[5].'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return NULL;

			foreach($servers as $id)
			{
				$copys = $sql->query('SELECT `id` FROM `control_copy` WHERE `status`="0"');
				while($copy = $sql->get($copys))
				{
					$sql->query('SELECT `uid` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
					$server = $sql->get();

					if(!sys::int($ssh->get('ps aux | grep copy_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
						$sql->query('UPDATE `control_copy` set `status`="1" WHERE `id`="'.$copy['id'].'" LIMIT 1');
				}
			}

			return NULL;
		}
	}
?>