<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(DATA.'boost.php');

	if($go)
	{
		$aData = array();

		$aData['site'] = isset($url['site']) ? $url['site'] : sys::outjs(array('e' => 'Необходимо указать сервис.'));

		// Проверка сервиса
		if(!in_array($aData['site'], $aBoost[$server['game']]['boost']))
			sys::outjs(array('e' => 'Указанный сервис по раскрутке не найден.'));

		if(isset($url['rating']))
		{
			$rating = $url['rating'] == 'up' ? '1' : '-1';

			$sql->query('SELECT `id` FROM `boost_rating` WHERE `boost`="'.$aData['site'].'" AND `user`="'.$user['id'].'" AND `rating`="'.$rating.'" LIMIT 1');
			if($sql->num())
				sys::out('err');

			$sql->query('DELETE FROM `boost_rating` WHERE `boost`="'.$aData['site'].'" AND `user`="'.$user['id'].'" LIMIT 1');
			$sql->query('INSERT INTO `boost_rating` set `boost`="'.$aData['site'].'", `rating`="'.$rating.'", `user`="'.$user['id'].'"');

			$sql->query('SELECT SUM(`rating`) FROM `boost_rating` WHERE `boost`="'.$aData['site'].'"');
			$sum = $sql->get();

			$rating = (int) $sum['SUM(`rating`)'];

			sys::out($rating, 'ctrl_server_boost_'.$sid);
		}

		$aData['service'] = isset($url['service']) ? sys::int($url['service']) : sys::outjs(array('e' => 'Необходимо указать номер услуги.'));

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

		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'ctrl_buy_boost'),
			array('circles' => $aBoost[$server['game']][$aData['site']]['circles'][$aData['service']],
				'money' => $sum, 'site' => $aBoost[$server['game']][$aData['site']]['site'], 'id' => $id)).'", `date`="'.$start_point.'", `type`="boost", `money`="'.$sum.'"');

		$sql->query('INSERT INTO `control_boost` set `user`="'.$user['id'].'", `server`="'.$sid.'", `site`="'.$aData['site'].'", `circles`="'.$aBoost[$server['game']][$aData['site']]['circles'][$aData['service']].'", `money`="'.$sum.'", `date`="'.$start_point.'"');

		sys::outjs(array('s' => 'ok'), $name_mcache);
	}

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Список игровых серверов #'.$id, $cfg['http'].'control/id/'.$id);
	$html->nav($server['address'], $cfg['http'].'control/id/'.$id.'/server/'.$sid);
    $html->nav('Раскрутка');

    if($mcache->get('ctrl_server_boost_'.$sid) != '')
        $html->arr['main'] = $mcache->get('ctrl_server_boost_'.$sid);
    else{
        $html->get('boost', 'sections/control/servers/'.$server['game']);

            $html->set('id', $id);
            $html->set('server', $sid);
            $html->set('address', $server['address']);

			foreach($aBoost[$server['game']]['boost'] as $boost)
			{
				$sql->query('SELECT SUM(`rating`) FROM `boost_rating` WHERE `boost`="'.$boost.'"');
				$sum = $sql->get();

				$rating = (int) $sum['SUM(`rating`)'];

				$html->set($boost, $rating);
			}

        $html->pack('main');

        $mcache->set('ctrl_server_boost_'.$sid, $html->arr['main'], false, 4);
    }
?>