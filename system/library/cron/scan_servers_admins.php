<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scan_servers_admins extends cron
	{
		function __construct()
		{
			global $sql, $argv, $start_point;

			$servers = $argv;

			unset($servers[0], $servers[1], $servers[2]);

			$game = $servers[3];

			if(!array_key_exists($game, cron::$admins_file))
				return NULL;

			$sql->query('SELECT `address` FROM `units` WHERE `id`="'.$servers[4].'" LIMIT 1');
			if(!$sql->num())
				return NULL;

			$unit = $sql->get();

			unset($servers[3], $servers[4]);

			$sql->query('SELECT `unit` FROM `servers` WHERE `id`="'.$servers[5].'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return NULL;

			foreach($servers as $id)
			{
				$sql->query('SELECT `uid`, `tarif` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
				$server = $sql->get();

				$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
				$tarif = $sql->get();

				$admins = $sql->query('SELECT `id`, `text` FROM `admins_'.$game.'` WHERE `server`="'.$id.'" AND `active`="1" AND `time`<"'.$start_point.'"');

				if(!$sql->num($admins))
					continue;

				$cmd = 'cd '.$tarif['install'].$server['uid'].';';

				while($admin = $sql->get($admins))
				{
					$cmd .= 'sed -i -e \'s/'.escapeshellcmd(htmlspecialchars_decode($admin['text'])).'//g\' '.cron::$admins_file[$game].';';

					$sql->query('UPDATE `admins_'.$game.'` set `active`="0" WHERE `id`="'.$admin['id'].'" LIMIT 1');
				}

				$cmd .= 'sed -i '."'/./!d'".' '.cron::$admins_file[$game].'; echo -e "\n" >> '.cron::$admins_file[$game].';';
				$cmd .= 'chown server'.$server['uid'].':1000 '.cron::$admins_file[$game];

				$ssh->set($cmd);
			}

			return NULL;
		}
	}
?>