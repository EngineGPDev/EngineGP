<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	DEFINE('USER_DATABASE', 'root');
	DEFINE('PASSWORD_DATABASE', 'SQLPASS'); // пароль mysql
	DEFINE('NAME_DATABASE', 'enginegp');
	DEFINE('CONNECT_DATABASE', 'localhost');
	DEFINE('ERROR_DATABASE', FALSE);
?>