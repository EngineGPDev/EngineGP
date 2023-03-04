<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aGroup = array(
		'admin' => 'Администратор',
		'support' => 'Техническая поддержка',
		'user' => 'Клиент'
	);

	if($id)
	{
		$nmch = 'read_help_'.$id;

		$cache = $mcache->get($nmch);

		// Если кеш создан
		if($cache)
		{
			$cache[$user['id']] = $user['group'].'|'.$start_point;

			$mcache->replace($nmch, $cache, false, 10);
		}else
			$mcache->set($nmch, array($user['id'] => $user['group'].'|'.$start_point), false, 10);

		if($user['group'] == 'user')
			sys::out('У вас нет доступа к данной информации.');

		// Обработка кеша
		$cache = $mcache->get($nmch);

		$read_now = '';

		foreach($cache as $reader => $data)
		{
			list($group, $time) = explode('|', $data);

			if($time+9 > $start_point)
				$read_now .= '<a href="#'.$reader.'" target="_blank">#'.$reader.' ('.$aGroup[$group].')</a>, ';
		}

		if(isset($read_now{1}))
			$read_now = substr($read_now, 0, -2);
			
		sys::out($read_now);
	}

	sys::out('Необходимо передать номер вопроса.');
?>