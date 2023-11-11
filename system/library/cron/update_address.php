<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class update_address extends cron
	{
		function __construct()
		{
			global $cfg, $sql, $start_point;

			$add_buys = $sql->query('SELECT `id`, `aid`, `server` FROM `address_buy` WHERE `time`<"'.$start_point.'"');

			while($add_buy = $sql->get($add_buys))
			{
				$sql->query('SELECT `unit`, `port`, `game`, `status` FROM `servers` WHERE `id`="'.$add_buy['server'].'" LIMIT 1');
				if($sql->num())
				{
					$server = $sql->get();

					if(!$cfg['buy_address'][$server['game']])
						continue;

					$sql->query('SELECT `address` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
					$unit = $sql->get();

					include(LIB.'games/games.php');

					// Очистка правил FireWall
					games::iptables($add_buy['server'], 'remove', NULL, NULL, $server['unit'], false);

					$sql->query('UPDATE `servers` set `address`="'.(sys::first(explode(':', $unit['address']))).':'.$server['port'].'" WHERE `id`="'.$add_buy['server'].'" LIMIT 1');

					if(in_array($server['status'], array('working', 'start', 'restart', 'change')))
						exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action restart '.$server['game'].' '.$add_buy['server'].'"');
				}

				$sql->query('UPDATE `address` set `buy`="0" WHERE `id`="'.$add_buy['aid'].'" LIMIT 1');
				$sql->query('DELETE FROM `address_buy` WHERE `id`="'.$add_buy['id'].'" LIMIT 1');
			}

			return NULL;
		}
	}
?>