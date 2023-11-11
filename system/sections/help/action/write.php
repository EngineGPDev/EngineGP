<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aGroup = array(
		'admin' => 'Администратор',
		'support' => 'Техническая поддержка',
		'user' => 'Клиент'
	);

	$write_st = isset($url['write']) ? true : false;

	if($id)
	{
		$nmch = 'write_help_'.$id;

		$cache = $mcache->get($nmch);

		// Если кеш создан
		if($cache)
		{
			if($write_st)
				$cache[$user['id']] = $user['group'].'|'.$start_point;
			else
				unset($cache[$user['id']]);

			$mcache->replace($nmch, $cache, false, 10);
		}else{
			if($write_st)
				$mcache->set($nmch, array($user['id'] => $user['group'].'|'.$start_point), false, 10);
		}

		if($user['group'] == 'user')
			sys::out('У вас нет доступа к данной информации.');

		// Обработка кеша
		$cache = $mcache->get($nmch);

		$write_now = '';

		if(is_array($cache))
			foreach($cache as $writer => $data)
			{
				list($group, $time) = explode('|', $data);

				if($time+9 > $start_point)
					$write_now .= '<a href="#'.$writer.'" target="_blank">#'.$writer.' ('.$aGroup[$group].')</a>, ';
			}

		if(isset($write_now{1}))
			$write_now = substr($write_now, 0, -2);

		sys::out($write_now);
	}

	sys::out('Необходимо передать номер вопроса.');
?>