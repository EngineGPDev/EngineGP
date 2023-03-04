<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scan_control extends cron
	{
		function __construct()
		{
			global $cfg, $sql;

			include(LIB.'ssh.php');
			include(LIB.'control/control.php');

			$sql->query('SELECT `id` FROM `control` ORDER BY `id` ASC');
			while($ctrl = $sql->get())
				ctrl::update_status($ctrl['id'], $ssh);

			return NULL;
		}
	}
?>