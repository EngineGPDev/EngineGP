<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Проверка на авторизацию
	sys::noauth($auth, $go);

	sys::cookie('egp_login', 'quit', -1);
	sys::cookie('egp_passwd', 'quit', -1);
	sys::cookie('egp_authkeycheck', 'quit', -1);

	// Обновление активности
	$sql->query('UPDATE `users` set `time`="'.($start_point-10).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

	sys::users($users, $user, $authkey, true);
	sys::back($cfg['http']);
?>