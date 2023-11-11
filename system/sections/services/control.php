<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(LIB.'games/games.php');

	// Обработка заказа
	if($go)
	{
		// Проверка на авторизацию
		sys::noauth();

		if($mcache->get('buy_server'))
			sleep(1.5);

		$mcache->set('buy_server', true, false, 3);

		$aData = array(
			'address' => isset($_POST['address']) ? trim($_POST['address']) : 0,
			'passwd' => isset($_POST['passwd']) ? $_POST['passwd'] : 0,
			'time' => isset($_POST['time']) ? sys::int($_POST['time']) : 30,
			'limit' => isset($_POST['limit']) ? sys::int($_POST['limit']) : key(array_shift($cfg['control_limit']))
		);

		if(sys::valid($aData['address'], 'ip'))
			sys::outjs(array('e' => 'Указанный адрес имеет неправильный формат'));

		$sql->query('SELECT `id` FROM `control` WHERE `address`="'.$aData['address'].'" LIMIT 1');
		if($sql->num())
			sys::outjs(array('e' => 'Данный сервер уже подключен'));

		if(sys::strlen($aData['passwd']) > 32)
			sys::outjs(array('e' => 'Указанный пароль слишком длинный'));

		if(sys::valid($aData['passwd'], 'other', $aValid['passwd']))
			sys::outjs(array('e' => 'Пожалуйста, поменяйте пароль используя только латинские буквы и цифры'));

		if(!array_key_exists($aData['limit'], $cfg['control_limit']))
			$aData['limit'] = key(array_shift($cfg['control_limit']));

		if(!in_array($aData['time'], $cfg['control_time']))
			$aData['time'] = array_shift($cfg['control_time']);

		$sum = games::define_sum(false, $cfg['control_limit'][$aData['limit']], 1, $aData['time']);

		// Проверка баланса
		if($user['balance'] < $sum)
			sys::outjs(array('e' => 'У вас не хватает '.(round($sum-$user['balance'], 2)).' '.$cfg['currency']));

		include(LIB.'ssh.php');

		// Проверка ssh соединения с физ. сервером
		if(!$ssh->auth($aData['passwd'], $aData['address']))
			sys::outjs(array('e' => 'Не удалось создать связь с физическим сервером, проверьте адрес и пароль'));
		
		// Списание средств с баланса пользователя
		$sql->query('UPDATE `users` set `balance`="'.($user['balance']-$sum).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

		// Реф. система
		games::part($user['id'], $sum);

		$days = $cfg['settlement_period'] ? games::define_period('buy', params::$aDayMonth) : $aData['time'];

		$sql->query('INSERT INTO `control` set '
			.'`user`="'.$user['id'].'",'
			.'`address`="'.$aData['address'].'",'
			.'`passwd`="'.$aData['passwd'].'",'
			.'`time`="'.($start_point+$days*86400).'",'
			.'`date`="'.$start_point.'",'
			.'`limit`="'.$aData['limit'].'",'
			.'`price`="'.$sum.'",'
			.'`status`="install"');

		$id = $sql->id();

		// Запись логов
		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'buy_control'), array('days' => games::parse_day($days, true), 'money' => $sum, 'id' => $id)).'", `date`="'.$start_point.'", `type`="buy", `money`="'.$sum.'"');

		sys::outjs(array('s' => 'ok', 'id' => $id));
	}

	if(isset($url['get']))
	{
		if(!isset($url['time']) || !in_array($url['time'], $cfg['control_time']))
			$url['time'] = array_shift($cfg['control_time']);

		if(!isset($url['limit']) || !array_key_exists($url['limit'], $cfg['control_limit']))
			$url['limit'] = key(array_shift($cfg['control_limit']));

		sys::out(games::define_sum(false, $cfg['control_limit'][$url['limit']], 1, $url['time']));
	}

	$options = '';

	foreach($cfg['control_time'] as $time)
		$options .= '<option value="'.$time.'">'.games::parse_day($time, true).'</option>';

	$limits = '';

	foreach($cfg['control_limit'] as $limit => $price)
		$limits .= '<option value="'.$limit.'">Серверов: '.$limit.' шт. / '.$price.' '.$cfg['currency'].'</option>';

	$html->get('index', 'sections/services/control');
		$html->set('time', $options);
		$html->set('limit', $limits);
		$html->set('cur', $cfg['currency']);
		if($cfg['settlement_period'])
		{
			$html->set('date', date('d.m.Y', $start_point));
			$html->unit('settlement_period', true, true);
		}else
			$html->unit('settlement_period', false, true);
	$html->pack('main');
?>