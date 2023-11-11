<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class users_recovery_signup extends cron
	{
		function __construct()
		{
			global $sql, $start_point;

			$time = $start_point-86400;

			$sql->query('DELETE FROM `signup` WHERE `date`<"'.$time.'"');
			$sql->query('DELETE FROM `recovery` WHERE `date`<"'.$time.'"');

			return NULL;
		}
	}
?>