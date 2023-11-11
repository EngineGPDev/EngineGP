<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($nmch))
		$nmch = false;

	// Проверка наличия арендованного выделенного адреса
	$sql->query('SELECT `id` FROM `address_buy` WHERE `server`="'.$id.'" LIMIT 1');
	if($sql->num() AND $go)
		sys::outjs(array('s' => 'ok'), $nmch);

	$aid = isset($url['aid']) ? sys::int($url['aid']) : sys::outjs(array('e' => 'Переданы не все данные'), $nmch);

	$sql->query('SELECT `ip`, `price` FROM `address` WHERE `id`="'.$aid.'" AND `unit`="'.$server['unit'].'" AND `buy`="0" LIMIT 1');

	if(!$sql->num())
		sys::outjs(array('e' => 'Выделенный адрес не найден.'), $nmch);

	$add = $sql->get();

	// Выполнение операции
	if($go)
	{
		// Проверка баланса
		if($user['balance'] < $add['price'])
			sys::outjs(array('e' => 'У вас не хватает '.(round($add['price']-$user['balance'], 2)).' '.$cfg['currency']), $nmch);

		include(LIB.'ssh.php');

		$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
		$unit = $sql->get();

		// Проверка ssh соединения с локацией
		if(!$ssh->auth($unit['passwd'], $unit['address']))
			sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

		// Списание средств с баланса пользователя
		$sql->query('UPDATE `users` set `balance`="'.($user['balance']-$add['price']).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

		// Реф. система
		games::part($user['id'], $add['price']);

		// Обновление информации
		$sql->query('UPDATE `address` set `buy`="1" WHERE `id`="'.$aid.'" LIMIT 1');
		$sql->query('UPDATE `servers` set `address`="'.$add['ip'].':'.params::$aDefPort[$server['game']].'" WHERE `id`="'.$id.'" LIMIT 1');

		$sql->query('INSERT INTO `address_buy` set `aid`="'.$aid.'", `server`="'.$id.'", `time`="'.($start_point+2592000).'"');

		// Порт игрового сервера
		$port = explode(':', $server['address']);

		// Очистка правил FireWall
		games::iptables($server['id'], 'remove', NULL, NULL, NULL, false, $ssh);

		// Запись логов
		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'buy_address'),
			array('money' => $add['price'], 'id' => $id)).'", `date`="'.$start_point.'", `type`="buy", `money`="'.$add['price'].'"');

		sys::outjs(array('s' => 'ok'), $nmch);
	}

	sys::outjs(array('s' => $add['price']));
?>