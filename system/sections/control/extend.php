<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `price`, `time` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$ctrl = $sql->get();

	include(LIB.'games/games.php');

	if($go)
	{
		if(!isset($url['time']) || !in_array($url['time'], $cfg['control_time']))
			$url['time'] = array_shift($cfg['control_time']);

		$sum = games::define_sum(false, $ctrl['price'], 1, $url['time']);

		// Проверка баланса
		if($user['balance'] < $sum)
			sys::outjs(array('e' => 'У вас не хватает '.(round($sum-$user['balance'], 2)).' '.$cfg['currency']));

		// Списание средств с баланса пользователя
		$sql->query('UPDATE `users` set `balance`="'.($user['balance']-$sum).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

		$time = $ctrl['time'] < $start_point ? $url['time']*86400 : $url['time']*86400+$ctrl['time'];

		// Обновление информации
		$sql->query('UPDATE `control` set `time`="'.$time.'" WHERE `id`="'.$id.'" LIMIT 1');

		// Запись логов
		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'extend_control'), array('days' => games::parse_day($days, true), 'money' => $sum, 'id' => $id)).'", `date`="'.$start_point.'", `type`="extend", `money`="'.$sum.'"');

		sys::outjs(array('s' => 'ok'));
	}

	if(isset($url['get']))
	{
		if(!isset($url['time']) || !in_array($url['time'], $cfg['control_time']))
			$url['time'] = array_shift($cfg['control_time']);

		sys::out(games::define_sum(false, $ctrl['price'], 1, $url['time']));
	}

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Подключенный сервер #'.$id, $cfg['http'].'control/id/'.$id);
	$html->nav('Продление аренды');

	$options = '';

	foreach($cfg['control_time'] as $time)
		$options .= '<option value="'.$time.'">'.games::parse_day($time, true).'</option>';

	$html->get('extend', 'sections/control');
		$html->set('id', $id);
		$html->set('time', $options);
		$html->set('price', $ctrl);
		$html->set('cur', $cfg['currency']);

		if($cfg['settlement_period'])
		{
			$html->set('date', date('d.m.Y', $start_point));
			$html->unit('settlement_period', true, true);
		}else
			$html->unit('settlement_period', false, true);
	$html->pack('main');
?>