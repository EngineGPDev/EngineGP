<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	// Проверка на наличие уже установленной выбранной услуги
	switch($aWebInstall[$server['game']][$url['subsection']])
	{
		case 'server':
			$sql->query('SELECT `id`, `login`, `user` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `server`="'.$id.'" LIMIT 1');
			break;

		case 'user':
			$sql->query('SELECT `id`, `login`, `user` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" LIMIT 1');
			break;

		case 'unit':
			$sql->query('SELECT `id`, `login`, `user` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');
			break;
	}

	if(!$sql->num())
		sys::outjs(array('i' => 'Дополнительная услуга не установлена.'), $nmch);

	$web = $sql->get();

	$sql->query('SELECT `mail` FROM `users` WHERE `id`="'.$web['user'].'" LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('e' => 'Необходимо указать пользователя доп. услуги.'), $nmch);

	$u = $sql->get();

	$passwd = sys::passwd($aWebParam[$url['subsection']]['passwd']);

	// Смена пароля вирт. хостинга
	$result = json_decode(file_get_contents(sys::updtext($aWebUnit['isp']['account']['passwd'],
		array('login' => $web['login'],
			'mail' => $u['mail'],
			'hdd' => $aWebUnit['isp']['hdd'],
			'passwd' => $passwd)
		)), true);

	if(!isset($result['result']) || strtolower($result['result']) != 'ok')
		sys::outjs(array('e' => 'Не удалось изменить пароль виртуального хостинга, обратитесь в тех.поддержку.'), $nmch);

	// Обновление данных
	$sql->query('UPDATE `web` set `passwd`="'.$passwd.'" WHERE `id`="'.$web['id'].'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>