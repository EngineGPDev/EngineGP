
<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Выполнение продления
	if($go)
	{
		$sql->query('SELECT `id`, `aid`, `time` FROM `address_buy` WHERE `server`="'.$id.'" LIMIT 1');

		if(!$sql->num())
			sys::outjs(array('s' => 'ok'), $nmch);

		$add = $sql->get();

		$sql->query('SELECT `price` FROM `address` WHERE `id`="'.$add['aid'].'" LIMIT 1');

		$add = array_merge($add, $sql->get());

		// Проверка баланса
		if($user['balance'] < $add['price'])
			sys::outjs(array('e' => 'У вас не хватает '.(round($add['price']-$user['balance'], 2)).' '.$cfg['currency']), $nmch);

		// Списание средств с баланса пользователя
		$sql->query('UPDATE `users` set `balance`="'.($user['balance']-$add['price']).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

		// Реф. система
		games::part($user['id'], $add['price']);

		// Обновление информации
		$sql->query('UPDATE `address_buy` set `time`="'.($add['time']+2592000).'" WHERE `id`="'.$add['id'].'" LIMIT 1');

		// Запись логов
		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'extend_address'),
			array('money' => $add['price'], 'id' => $id)).'", `date`="'.$start_point.'", `type`="extend", `money`="'.$add['price'].'"');

		sys::outjs(array('s' => 'ok'), $nmch);
	}
?>