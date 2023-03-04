<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class graph_servers_hour extends cron
	{
		function __construct()
		{
			global $sql, $start_point;

			$servers = $sql->query('SELECT `id`, `online`, `ram_use`, `cpu_use`, `hdd_use`, `date` FROM `servers` ORDER BY `id` ASC');

			while($server = $sql->get($servers))
			{
				if($server['date']+3600 > $start_point)
					continue;

				$sql->query('INSERT INTO `graph_hour` set `server`="'.$server['id'].'",'
						.'`online`="'.$server['online'].'",'
						.'`cpu`="'.$server['cpu_use'].'",'
						.'`ram`="'.$server['ram_use'].'",'
						.'`hdd`="'.$server['hdd_use'].'", `time`="'.$start_point.'"');
			}

			return NULL;
		}
	}
?>