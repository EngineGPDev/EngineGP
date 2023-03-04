<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class control_threads extends cron
	{
		function __construct()
		{
			global $sql, $cfg, $argv;

			$aUnit = array();
			$sql->query('SELECT `id` FROM `control` ORDER BY `id` ASC');

			if(!$sql->num())
				return NULL;

			while($unit = $sql->get())
				$aUnit[$unit['id']] = '';

			$sql->query('SELECT `id` FROM `control_servers` LIMIT 1');

			if(!$sql->num())
				return NULL;

			$sql->query('SELECT `id`, `unit`, `game` FROM `control_servers` ORDER BY `unit` DESC');

			$all = $sql->num();

			while($server = $sql->get())
				$aUnit[$server['unit']][$server['game']] .= $server['id'].' ';

			if($argv[3] == 'control_scan_servers_route')
				cron::$seping = 50;

			foreach($aUnit as $unit => $aGame)
			{
				foreach($aGame as $game => $servers)
				{
					$aData = explode(' ', $servers);

					$num = count($aData)-1;
					$sep = $num > 0 ? ceil($num/cron::$seping) : 1;

					unset($aData[end($aData)]);

					$threads[] = cron::thread($sep, $game.' '.$unit, $aData);
				}
			}

			$cmd = '';

			foreach($threads as $thread)
			{
				foreach($thread as $screen => $servers)
					$cmd .= 'sudo -u www-data screen -dmS scan_'.(sys::first(explode(' ', $servers))).'_'.$screen.' taskset -c '.$cfg['cron_taskset'].' sh -c \"cd /var/enginegp; php cron.php '.$cfg['cron_key'].' '.$argv[3].' '.$servers.'\"; sleep 1;';
			}

			exec('screen -dmS control_threads_'.date('His', $start_point).' sh -c "'.$cmd.'"');

			return NULL;
		}
	}
?>