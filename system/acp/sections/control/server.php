<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `time`, `overdue` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$ctrl = $sql->get();

	if($ctrl['time'] > $start_point AND $ctrl['overdue'])
		$sql->query('UPDATE `control` set `overdue`="0" WHERE `id`="'.$id.'" LIMIT 1');

	$sql->query('SELECT * FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$ctrl = $sql->get();

	if($go)
	{
		if(isset($url['type']) AND in_array($url['type'], array('overdue', 'block', 'tarif')))
		{
			if($url['type'] != 'tarif')
			{
				$time = isset($_POST['time']) ? trim($_POST['time']) : sys::outjs(array('e' => 'Необходимо указать дату.'));

				$date = sys::checkdate($time);
			}

			switch($url['type'])
			{
				case 'overdue':
					if($ctrl['time'] > $start_point)
						sys::outjs(array('e' => 'Игровой сервер должен быть просрочен.'));

					$sql->query('UPDATE `control` set `overdue`="'.$date.'" WHERE `id`="'.$id.'" LIMIT 1');
				break;

				case 'block':
					if($ctrl['status'] != ('off' || 'overdue'))
						sys::outjs(array('e' => 'Игровой сервер должен быть выключен.'));

					if($date < $start_point)
						$sql->query('UPDATE `control` set `status`="off", `block`="0" WHERE `id`="'.$id.'" LIMIT 1');
					else
						$sql->query('UPDATE `control` set `status`="blocked", `block`="'.$date.'" WHERE `id`="'.$id.'" LIMIT 1');
			}

			sys::outjs(array('s' => 'ok'));
		}

		$aData = array();

		$aData['user'] = isset($_POST['user']) ? sys::int($_POST['user']) : $ctrl['user'];
		$aData['address'] = isset($_POST['address']) ? trim($_POST['address']) : $ctrl['address'];
		$aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : $ctrl['passwd'];
		$aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : $ctrl['time'];
		$aData['sql_passwd'] = isset($_POST['sql_passwd']) ? trim($_POST['sql_passwd']) : $ctrl['sql_passwd'];
		$aData['sql_ftp'] = isset($_POST['sql_ftp']) ? trim($_POST['sql_ftp']) : $ctrl['sql_ftp'];
		$aData['limit'] = isset($_POST['sql_ftp']) ? sys::int($_POST['limit']) : $ctrl['limit'];
		$aData['price'] = isset($_POST['price']) ? sys::int($_POST['price']) : $ctrl['price'];

		include(LIB.'ssh.php');

		if(sys::valid($aData['address'].':22', 'other', $aValid['address']))
			$aData['address'] = $ctrl['address'];

		if(sys::valid($aData['sql_passwd'], 'en'))
			$aData['sql_passwd'] = $ctrl['sql_passwd'];

		if(sys::valid($aData['sql_ftp'], 'en'))
			$aData['sql_ftp'] = $ctrl['sql_ftp'];

		if(!$ssh->auth($aData['passwd'], $aData['address']))
			sys::outjs(array('e' => 'Не удалось создать связь с локацией'));

		if($ctrl['user'] != $aData['user'])
		{
			$sql->query('SELECT `id` FROM `users` WHERE `id`="'.$aData['user'].'" LIMIT 1');
			if(!$sql->num())
				sys::outjs(array('e' => 'Пользователь не найден.'));
		}

		$aData['time'] = sys::checkdate($aData['time']);

		$sql->query('UPDATE `control` set '
			.'`user`="'.$aData['user'].'",'
			.'`address`="'.$aData['address'].'",'
			.'`passwd`="'.$aData['passwd'].'",'
			.'`time`="'.$aData['time'].'",'
			.'`sql_passwd`="'.$aData['sql_passwd'].'",'
			.'`sql_ftp`="'.$aData['sql_ftp'].'",'
			.'`limit`="'.$aData['limit'].'",'
			.'`price`="'.$aData['price'].'" WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('server', 'sections/control');
		$html->set('id', $id);
		$html->set('user', $ctrl['user']);
		$html->set('address', $ctrl['address']);
		$html->set('passwd', $ctrl['passwd']);
		$html->set('sql_passwd', $ctrl['sql_passwd']);
		$html->set('sql_ftp', $ctrl['sql_ftp']);
		$html->set('limit', $ctrl['limit']);
		$html->set('price', $ctrl['price']);
		$html->set('time', date('d/m/Y H:i', $ctrl['time']));
		$html->set('date', date('d.m.Y - H:i:s', $ctrl['date']));
		$html->set('overdue', $ctrl['overdue'] == 0 ? 'Установить' : date('d/m/Y H:i', $ctrl['overdue']));
		$html->set('block', $ctrl['block'] == 0 ? 'Заблокировать' : date('d/m/Y H:i', $ctrl['block']));

	$html->pack('main');
?>