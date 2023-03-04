<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aAccessI = array(
		'start' => 'Включение',
		'stop' => 'Выключение',
		'restart' => 'Перезагрузка',
		'change' => 'Смена карты',
		'reinstall' => 'Переустановка',
		'update' => 'Обновление',
		'console' => 'Раздел "Консоль"',
		'settings' => 'Раздел "Настройки"',
		'plugins' => 'Раздел "Плагины"',
		'maps' => 'Раздел "Карты"'
	);

	$aAccess = array('start', 'stop', 'restart', 'change', 'reinstall', 'update', 'console', 'settings', 'plugins', 'maps');

	// Проверка прав
	if(isset($url['rights']) AND $url['rights'] > 0)
	{
		$sql->query('SELECT `rights` FROM `owners` WHERE `id`="'.sys::int($url['rights']).'" AND `server`="'.$id.'" LIMIT 1');

		if(!$sql->num())
			sys::outjs(array('e' => 'Совладелец не найден.'));

		$owner = $sql->get();

		$aRights = sys::b64djs($owner['rights']);

		$rights = '';

		foreach($aAccess as $access)
			if($aRights[$access]) $rights .= $aAccessI[$access].', ';

		sys::outjs(array('s' => substr($rights, 0, -2)));
	}

	// Удаление совладельца
	if(isset($url['delete']) AND $url['delete'] > 0)
	{
		$sql->query('SELECT `rights` FROM `owners` WHERE `id`="'.sys::int($url['delete']).'" AND `server`="'.$id.'" LIMIT 1');

		if($sql->num())
			$sql->query('DELETE FROM `owners` WHERE `id`="'.sys::int($url['delete']).'" AND `server`="'.$id.'" LIMIT 1');

		sys::back($cfg['http'].'servers/id/'.$id.'/section/owners');
	}

	// Добавление совладельца
	if($go)
	{
		$nmch = sys::rep_act('server_owners_go_'.$id, 5);

		$aData = (isset($_POST['owner']) AND is_array($_POST['owner'])) ? $_POST['owner'] : array();

		$aDate = isset($aData['\'time\'']) ? explode('.', $aData['\'time\'']) : explode('.', date('d.m.Y', $start_point));
		$aTime = explode(':', date('H:i:s', $start_point));

		if(!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2]))
			sys::outjs(array('e' => 'Дата доступа указана неверно.'), $nmch);

		$time = mktime($aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2])+3600;

		if($time < $start_point)
			sys::outjs(array('e' => 'Время доступа не может быть меньше 60 минут.'), $nmch);

		// Проверка пользователя
		if(!isset($aData['\'user\'']))
			sys::outjs(array('e' => 'Необходимо указать пользователя.'), $nmch);

		if(is_numeric($aData['\'user\'']))
			$sql->query('SELECT `id` FROM `users` WHERE `id`="'.$aData['\'user\''].'" LIMIT 1');
		else{
			if(sys::valid($aData['\'user\''], 'other', $aValid['login']))
				sys::outjs(array('e' => sys::text('input', 'login_valid')), $nmch);

			$sql->query('SELECT `id` FROM `users` WHERE `login`="'.$aData['\'user\''].'" LIMIT 1');
		}

		if(!$sql->num())
			sys::outjs(array('e' => 'Пользователь не найден в базе.'), $nmch);

		$uowner = $sql->get();

		$owner = $sql->query('SELECT `id` FROM `owners` WHERE `server`="'.$id.'" AND `user`="'.$uowner['id'].'" LIMIT 1');

		// Если не обновление доступа совладельца, проверить кол-во
		if(!$sql->num($owner))
		{
			$sql->query('SELECT `id` FROM `owners` WHERE `server`="'.$id.'" LIMIT 5');

			if($sql->num() == 5)
				sys::outjs(array('e' => 'Вы добавили максимально число совладельцев.'), $nmch);
		}

		$sql->query('SELECT `id` FROM `servers` WHERE `id`="'.$id.'" AND `user`="'.$uowner['id'].'" LIMIT 1');
		if($sql->num())
			sys::outjs(array('e' => 'Владельца сервера нельзя добавить в совладельцы.'), $nmch);

		$aRights = array();

		$check = 0;

		foreach($aAccess as $access)
		{
			$aRights[$access] = isset($aData['\''.$access.'\'']) ? 1 : 0;

			$check += $aRights[$access];
		}

		if(!$check)
			sys::outjs(array('e' => 'Необходимо включить минимум одно разрешение.'), $nmch);



		if($sql->num($owner))
			$sql->query('UPDATE `owners` set `rights`="'.sys::b64js($aRights).'", `time`="'.$time.'" WHERE `server`="'.$id.'" AND `user`="'.$uowner['id'].'" LIMIT 1');
		else
			$sql->query('INSERT INTO `owners` set `server`="'.$id.'", `user`="'.$uowner['id'].'", `rights`="'.sys::b64js($aRights).'", `time`="'.$time.'"');

		sys::outjs(array('s' => 'ok'), $nmch);
	}

	$html->nav($server['address'], $cfg['http'].'servers/id/'.$id);

	$html->nav('Друзья');

	$cache = $mcache->get('server_owners_'.$id);

	if($cache != '')
		$html->arr['main'] = $cache;
	else{
		$owners = $sql->query('SELECT `id`, `user`, `rights`, `time` FROM `owners` WHERE `server`="'.$id.'" AND `time`>"'.$start_point.'" ORDER BY `id` ASC LIMIT 5');

		if($sql->num())
			include(LIB.'games/games.php');

		while($owner = $sql->get($owners))
		{
			$sql->query('SELECT `login` FROM `users` WHERE `id`="'.$owner['user'].'" LIMIT 1');
			if(!$sql->num())
				continue;

			$uowner = $sql->get();

			$rights = games::owners(sys::b64djs($owner['rights']));

			$html->get('owners_list', 'sections/servers/games');

				$html->set('id', $id);
				$html->set('oid', $owner['id']);
				$html->set('user', $uowner['login']);
				$html->set('rights', $rights);
				$html->set('time', date('d.m.Y - H:i', $owner['time']));

			$html->pack('owners');
		}

		$html->get('owners', 'sections/servers/'.$server['game']);

			$html->set('id', $id);
			$html->set('time', date('d.m.Y', $start_point));

			$html->set('owners', isset($html->arr['owners']) ? $html->arr['owners'] : 'Для данного сервера совладельцы отсутсвуют.');

		$html->pack('main');

		$mcache->set('server_owners_'.$id, $html->arr['main'], false, 1);
	}
?>