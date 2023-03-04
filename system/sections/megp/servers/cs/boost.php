<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aData = array();

		$aData['site'] = isset($url['site']) ? $url['site'] : sys::outjs(array('e' => 'Необходимо указать сервис.'));
		$aData['service'] = isset($url['service']) ? sys::int($url['service']) : sys::outjs(array('e' => 'Необходимо указать номер услуги.'));

		include(DATA.'boost.php');

		// Проверка сервиса
		if(!array_key_exists($aData['site'], $aBoost[$server['game']]))
			sys::outjs(array('e' => 'Указанный сервис по раскрутке не найден.'));

		// Проверка номера услуги
		if(!in_array($aData['service'], $aBoost[$server['game']][$aData['site']]['services']))
			sys::outjs(array('e' => 'Неправильно указан номер услуги.'));

		// Определение суммы
		$sum = $aBoost[$server['game']][$aData['site']]['price'][$aData['service']];

		// Проверка баланса
		if($user['balance'] < $sum)
			sys::outjs(array('e' => 'У вас не хватает '.(round($sum-$user['balance'], 2)).' '.$cfg['currency']), $name_mcache);

		include(LIB.'games/boost.php');

		$boost = new boost($aBoost[$server['game']][$aData['site']]['key'], $aBoost[$server['game']][$aData['site']]['api']);

		$buy = $boost->$aBoost[$server['game']][$aData['site']]['type'](array('period' => $aData['service'], 'address' => $server['address']));

		if(is_array($buy))
			sys::outjs(array('e' => $buy['error']));

		// Списание средств с баланса пользователя
		$sql->query('UPDATE `users` set `balance`="'.($user['balance']-$sum).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

		include(LIB.'games/games.php');

		// Реф. система
		games::part($user['id'], $sum);

		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'buy_boost'),
			array('circles' => $aBoost[$server['game']][$aData['site']]['circles'][$aData['service']],
				'money' => $sum, 'site' => $aBoost[$server['game']][$aData['site']]['site'], 'id' => $id)).'", `date`="'.$start_point.'", `type`="boost", `money`="'.$sum.'"');

		$sql->query('INSERT INTO `boost` set `user`="'.$user['id'].'", `server`="'.$id.'", `site`="'.$aData['site'].'", `circles`="'.$aBoost[$server['game']][$aData['site']]['circles'][$aData['service']].'", `money`="'.$sum.'", `date`="'.$start_point.'"');

		sys::outjs(array('s' => 'ok'), $name_mcache);
	}

    if($mcache->get('server_boost_'.$id) != '')
        $html->arr['main'] = $mcache->get('server_boost_'.$id);
    else{
        $html->get('boost', 'sections/servers/'.$server['game']);

            $html->set('id', $id);
            $html->set('address', $server['address']);
            $html->set('cur', $cfg['currency']);

        $html->pack('main');

        $mcache->set('server_boost_'.$id, $html->arr['main'], false, 4);
    }
?>