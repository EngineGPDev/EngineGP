<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($nmch))
		$nmch = false;

	$plan = isset($url['plan']) ? sys::int($url['plan']) : sys::outjs(array('e' => 'Переданые не все данные'), $nmch);

	$aPrice = explode(':', $tarif['price']);
	$aFPS = explode(':', $tarif['fps']);

	// Проверка плана
	if(array_search($plan, $aFPS) === FALSE)
		sys::outjs(array('e' => 'Переданы неверные данные'), $nmch);

	if($plan == $server['fps'])
		sys::outjs(array('e' => 'Смысла в этой операции нет'), $nmch);

	if(!tarif::price($tarif['price']))
		sys::outjs(array('e' => 'Чтобы изменить тариф, перейдите в настройки запуска'), $nmch);

	if($server['time'] < $start_point+86400)
		$time = $server['time'];
	else{
		// Цена за 1 день аренды (по новому тарифному плану)
		$price = $aPrice[array_search($plan, $aFPS)]/30*$server['slots'];

		// Цена за 1 день аренды (по старому тарифному плану)
		$price_old = $aPrice[array_search($server['fps'], $aFPS)]/30*$server['slots'];

		// Остаток дней аренды
		$days = ($server['time']-$start_point)/86400;

		$time = date('H:i:s', $server['time']);
		$date = date('d.m.Y', round($start_point+$days*$price_old/$price*86400-86400));

		$aDate = explode('.', $date);
		$aTime = explode(':', $time);

		$time = mktime($aTime[0], $aTime[1], $aTime[2], $aDate[1], $aDate[0], $aDate[2]);
	}

	// Выполнение смена тарифного плана
	if($go)
	{
		$sql->query('UPDATE `servers` set `time`="'.$time.'", `fps`="'.$plan.'" WHERE `id`="'.$id.'" LIMIT 1');

		if(in_array($server['status'], array('working', 'start', 'restart', 'change')))
		{
			include(LIB.'games/'.$server['game'].'/action.php');

			action::start($id, 'restart');
		}

		// Запись логов
		$sql->query('INSERT INTO `logs_sys` set `user`="'.$user['id'].'", `server`="'.$id.'", `text`="'.sys::text('syslogs', 'change_plan').'", `time`="'.$start_point.'"');

		sys::outjs(array('s' => 'ok'), $nmch);
	}

	// Выхлоп информации
	sys::outjs(array('s' => date('d.m.Y - H:i', $time).' ('.sys::date('min', $time).')'), $nmch);
?>