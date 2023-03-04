<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$name_mcache = 'cashback_'.$user['id'];

	// Проверка сессии
	if($mcache->get($name_mcache))
		sys::outjs(array('e' => $text['mcache']), $name_mcache);

	// Создание сессии
	$mcache->set($name_mcache, 1, false, 10);

	if(!$cfg['part_money'])
		sys::outjs(array('e' => 'Вывод средств невозможен'), $name_mcache);

	$aData = array();

	$aData['purse'] = isset($url['purse']) ? strtolower(trim($url['purse'])) : sys::outjs(array('e' => 'Необходимо указать кошелек'), $name_mcache);
	$aData['sum'] = isset($url['sum']) ? round(floatval($url['sum']), 2) : sys::outjs(array('e' => 'Необходимо указать сумму'), $name_mcache);

	$sql->query('SELECT `part_money` FROM `users` WHERE `id`="'.$user['id'].'" LIMIT 1');
	$user = array_merge($user, $sql->get());

	// Проверка доступной суммы
	if($aData['sum'] > $user['part_money'])
		sys::outjs(array('e' => 'У вас нет указанной суммы'), $name_mcache);

	if(!in_array($aData['purse'], array('phone', 'wmr', 'lk')))
		sys::outjs(array('e' => 'Неверно указан кошелек'), $name_mcache);

	// Вывод на баланс сайта
	if($aData['purse'] == 'lk')
	{
		if($aData['sum'] < 1)
			sys::outjs(array('e' => 'Сумма не должна быть меньше 1 '.$cfg['currency']), $name_mcache);

		$sql->query('UPDATE `users` set `balance`="'.($user['balance']+$aData['sum']).'", `part_money`="'.($user['part_money']-$aData['sum']).'" WHERE `id`="'.$user['id'].'"');
		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'cashback'),
				array('purse' => $cfg['part_log'], 'money' => $aData['sum'])).'", `date`="'.$start_point.'", `type`="cashback", `money`="'.$aData['sum'].'"');

		sys::outjs(array('s' => 'Перевод средств был успешно произведен'), $name_mcache);
	}

	// Проверка лимита на мин. сумму за перевод
	if($aData['sum'] < $cfg['part_limit_min'])
		sys::outjs(array('e' => 'Миниммальная сумма вывода '.$cfg['part_limit_min'].' '.$cfg['currency']), $name_mcache);

	// Проверка кошелька
	if($aData['purse'] == 'wmr')
	{
		$sql->query('SELECT `wmr` FROM `users` WHERE `id`="'.$user['id'].'" AND `wmr`!="" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Чтобы вывести деньги на WMR-кошелек, необходимо его указать в профиле'), $name_mcache);
	}else{
		$sql->query('SELECT `phone` FROM `users` WHERE `id`="'.$user['id'].'" AND `confirm_phone`="1" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Чтобы вывести деньги на QIWI, необходим подтвержденный номер в профиле'), $name_mcache);
	}

	$purse = $sql->get();

	// Вывод без одобрения
	if($cfg['part_output'])
	{
		// Проверка лимита на макс. сумму за 24 часа
		$sql->query('SELECT SUM(`money`) FROM `cashback` WHERE  `user`="'.$user['id'].'" AND `time`<"'.($start_point-86400).'" AND `status`="0"');
		$sum = $sql->get();

		if(($aData['sum']+$sum['SUM(`money`)']) > $cfg['part_limit_max'])
			sys::outjs(array('e' => 'Максимальная сумма вывода за 24 часа '.$cfg['part_limit_max'].' '.$cfg['currency']), $name_mcache);

		// Проверка общего лимита за 24 часа
		$sql->query('SELECT SUM(`money`) FROM `cashback` WHERE `time`<"'.($start_point-86400).'" AND `status`="0"');
		$sum = $sql->get();

		if(($aData['sum']+$sum['SUM(`money`)']) > $cfg['part_limit_day'])
			sys::outjs(array('e' => 'Общий лимит на вывод за 24 часа достигнут, попробуйте вывести завтра'), $name_mcache);

		// Запрос на шлюз
		if($cfg['part_gateway'] == 'unitpay')
		{
			$aType = array('phone' => 'qiwi', 'wmr' => 'webmoney');

			$sql->query('INSERT INTO `cashback` set `user`="'.$user['id'].'", `purse`="'.$purse[$aData['purse']].'", `money`="'.$aData['sum'].'", `date`="'.$start_point.'", `status`="0"');
			$id = $sql->id();

			$sum = $aData['sum']-($aData['sum']/100*$cfg['part_output_proc']);

			$json = file_get_contents('https://unitpay.ru/api?method=massPayment&params[sum]='.$sum.'&params[purse]='.$purse[$aData['purse']].'&params[login]='.$cfg['unitpay_mail'].'&params[transactionId]='.$id.' &params[secretKey]='.$cfg['unitpay_api'].'&params[paymentType]='.$aType[$aData['purse']]);

			$array = json_decode($json, true);

			// Упешный вывод средств
			if(is_array($array) AND isset($array['result']) AND in_array($array['result']['status'], array('success', 'not_completed ')))
			{
				$sql->query('UPDATE `users` set `part_money`="'.($user['part_money']-$aData['sum']).'" WHERE `id`="'.$user['id'].'" LIMIT 1');
				$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'cashback'),
				array('purse' => $aType[$aData['purse']], 'money' => $aData['sum'])).'", `date`="'.$start_point.'", `type`="cashback", `money`="'.$aData['sum'].'"');

				sys::outjs(array('s' => 'Запрос на вывод средств был успешно выполнен'), $name_mcache);
			}

			if(!is_array($array))
				sys::outjs(array('e' => 'Неудалось выполнить запрос'), $name_mcache);

			switch($array['error']['code'])
			{
				case '103':
					sys::outjs(array('e' => 'На данный момент вы не можете вывести средства, обратитесь к администратору'), $name_mcache);
				case '104':
					sys::outjs(array('e' => 'Номер телефона не входит в список доступных для выплат стран'), $name_mcache);
				case '1053':
					sys::outjs(array('e' => 'Платежная система не смогла получить информацию о номере телефона'), $name_mcache);
			}
		}

		sys::outjs(array('e' => 'Технические проблемы, обратитесь в службу поддержки'.$array['error']['code']), $name_mcache);
	}

	$sql->query('UPDATE `users` set `part_money`="'.($user['part_money']-$aData['sum']).'" WHERE `id`="'.$user['id'].'" LIMIT 1');
	$sql->query('INSERT INTO `cashback` set `user`="'.$user['id'].'", `purse`="'.$purse[$aData['purse']].'", `money`="'.$aData['sum'].'", `date`="'.$start_point.'", `status`="1"');

	sys::outjs(array('s' => 'Заявка на вывод средств была успешно создана'), $name_mcache);
?>