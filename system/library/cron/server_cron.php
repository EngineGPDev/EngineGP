<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class server_cron extends cron
	{
		function __construct()
		{
			global $argv, $sql, $cfg;

			$sql->query('SELECT `game` FROM `servers` WHERE `id`="'.$argv[3].'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `task` FROM `crontab` WHERE `id`="'.$argv[4].'" LIMIT 1');
			$cron = $sql->get();

			$cmd = $cron['task'] == 'console' ? ' '.$argv[4] : '';

			exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action '.$cron['task'].' '.$server['game'].' '.$argv[3].$cmd.'"');

			return NULL;
		}
	}
?>