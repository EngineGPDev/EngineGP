<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class preparing_web_delete extends cron
	{
		function __construct()
		{
			global $argv, $cfg, $sql;

			$sql->query('SELECT `id` FROM `web` WHERE `user`="0"');
			while($web = $sql->get())
				exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' web_delete '.$web['id'].'"');

			return NULL;
		}
	}
?>