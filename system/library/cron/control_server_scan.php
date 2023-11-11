<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class control_server_scan extends cron
	{
		function __construct()
		{
			global $argv;

			include(LIB.'control/'.$argv[3].'/scan.php');

			scan::mon($argv[4], true);
			scan::resources($argv[4]);

			return NULL;
		}
	}
?>