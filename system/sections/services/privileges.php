<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$nmch = 'privileges'.sys::ip();

		if($mcache->get($nmch))
            sys::outjs(array('e' => sys::text('other', 'mcache')), $nmch);

        $mcache->set($nmch, 1, false, 10);

		$aData = array();

		$aData['address'] = isset($_POST['address']) ? $_POST['address'] : sys::outjs(array('e' => 'Необходимо указать адрес сервера'), $nmch);
		$aData['type'] = isset($_POST['type']) ? $_POST['type'] : sys::outjs(array('e' => 'Необходимо указать тип авторизации на сервере'), $nmch);
		$aData['data'] = isset($_POST['data']) ? str_replace('"', '', $_POST['data']) : sys::outjs(array('e' => 'Необходимо указать данные авторизации'), $nmch);
		$aData['passwd'] = isset($_POST['passwd']) ? $_POST['passwd'] : '';
		$aData['service'] = isset($_POST['service']) ? sys::int($_POST['service']) : sys::outjs(array('e' => 'Необходимо указать услугу'), $nmch);
		$aData['time'] = isset($_POST['time']) ? sys::int($_POST['time']) : sys::outjs(array('e' => 'Необходимо указать период'), $nmch);
		$aData['mail'] = isset($_POST['mail']) ? $_POST['mail'] : sys::outjs(array('e' => 'Необходимо указать почту'), $nmch);

		if(!in_array($aData['type'], array('a', 'ca', 'de')))
			sys::outjs(array('e' => 'Неправильно передан тип авторизации на сервере'), $nmch);

		switch($aData['type'])
		{
			case 'a':
				if($aData['data'] == '')
					sys::outjs(array('e' => 'Необходимо указать ник'), $nmch);
				break;
			case 'ca':
				if(sys::valid($aData['data'], 'steamid') || sys::valid($aData['data'], 'steamid3'))
					sys::outjs(array('e' => 'Неправильный формат SteamID'), $nmch);
				break;
			default:
				if(sys::valid($aData['data'], 'ip'))
					sys::outjs(array('e' => 'Неправильный формат IP'), $nmch);
		}

		if(sys::valid($aData['address'], 'other', $aValid['address']))
			sys::outjs(array('e' => 'Адрес игрового сервера имеет неверный формат'), $nmch);

		$sql->query('SELECT `id`, `name`, `game` FROM `servers` WHERE `address`="'.$aData['address'].'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Игровой сервер не найден в базе'), $nmch);

		$server = $sql->get();

		$sql->query('SELECT `id` FROM `admins_'.$server['game'].'` WHERE `server`="'.$server['id'].'" AND `value`="'.htmlspecialchars($aData['data']).'" LIMIT 1');
		if($sql->num())
			sys::outjs(array('e' => 'Привилегия для данного игрока уже установлена, дождитесь её завершения.'), $nmch);

		if($aData['type'] != 'de' AND sys::valid($aData['passwd'], 'other', $aValid['passwd']))
			sys::outjs(array('e' => 'Неправильный формат пароля, используйте латинские буквы и цифры от 6 до 20 символов'), $nmch);

		if(sys::valid($aData['mail'], 'other', $aValid['mail']))
			sys::outjs(array('e' => 'Неправильный формат почты'), $nmch);

		$sql->query('SELECT `flags`, `immunity`, `data` FROM `privileges_list` WHERE `id`="'.$aData['service'].'" AND `server`="'.$server['id'].'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Указанная услуга не найдена'), $nmch);

		$privilege = $sql->get();

		$data = sys::b64djs($privilege['data']);

		if(!array_key_exists($aData['time'], $data))
			sys::outjs(array('e' => 'Неправильно указан период'), $nmch);

		$price = $data[$aData['time']];

		$time = !$aData['time'] ? $start_point+172800000 : $start_point+$aData['time']*86400;

		if($server['game'] == 'cs')
		{
			$text = '"'.$aData['data'].'" "'.$aData['passwd'].'" "'.$privilege['flags'].'" "'.$aData['type'].'"';
			$sqlq = 'INSERT INTO `admins_'.$server['game'].'` set'
					.'`server`="'.$server['id'].'",'
					.'`value`="'.htmlspecialchars($aData['data']).'",'
					.'`active`="1",'
					.'`passwd`="'.$aData['passwd'].'",'
					.'`flags`="'.$privilege['flags'].'",'
					.'`type`="'.$aData['type'].'",'
					.'`time`="'.$time.'",'
					.'`text`="'.htmlspecialchars($text).'",'
					.'`info`="Онлайн покупка"';
		}else{
			$text = '"'.$aData['data'].'" "'.$aData['immunity'].':'.$privilege['flags'].'" "'.$aData['passwd'].'"';
			$sqlq = 'INSERT INTO `admins_'.$server['game'].'` set'
					.'`server`="'.$server['id'].'",'
					.'`value`="'.$aData['data'].'",'
					.'`active`="1",'
					.'`passwd`="'.htmlspecialchars($aData['passwd']).'",'
					.'`flags`="'.$aData['flags'].'",'
					.'`immunity`="'.$privilege['immunity'].'",'
					.'`time`="'.$time.'",'
					.'`text`="'.htmlspecialchars($text).'",'
					.'`info`="Онлайн покупка"';
		}

		$sql->query('SELECT `key` FROM `privileges_buy` WHERE '
			.'`server`="'.$server['id'].'" AND'
			.'`text`="'.base64_encode($text).'" AND'
			.'`price`="'.$price.'" AND'
			.'`mail`="'.$aData['mail'].'" AND'
			.'`status`="0" LIMIT 1');

		if(!$sql->num())
		{
			$key = sys::key();

			$sql->query('INSERT INTO `privileges_buy` set '
					.'`server`="'.$server['id'].'",'
					.'`text`="'.base64_encode($text).'",'
					.'`sql`="'.base64_encode($sqlq).'",'
					.'`price`="'.$price.'",'
					.'`key`="'.$key.'",'
					.'`date`="'.$start_point.'",'
					.'`mail`="'.$aData['mail'].'",'
					.'`status`="0"');
		}else{
			$pay = $sql->get();
			$key = $pay['key'];
		}		

		$html->get('pay', 'sections/services/privileges');

			$html->set('cur', $cfg['currency']);
			$html->set('wmr', $cfg['webmoney_wmr']);
			$html->set('key', $key);
			$html->set('sum', $price);

		$html->pack('pay');

		sys::outjs(array('s' => $html->arr['pay']), $nmch);
	}

	if(isset($url['select']))
	{
		if($url['select'] == 'time')
		{
			$service = isset($url['service']) ? sys::int($url['service']) : sys::out();

			$sql->query('SELECT  `data` FROM `privileges_list` WHERE `id`="'.$service.'" LIMIT 1');
			$list = $sql->get();

			$time = '';

			$data = sys::b64djs($list['data']);

			if(isset($data[0]))
			{
				$time = '<option value="0">Навсегда / '.$data[0].' '.$cfg['currency'].'</option>';

				unset($data[0]);
			}

			foreach($data as $days => $price)
				$time .= '<option value="'.$days.'">'.$days.' '.sys::day($time).' / '.$price.' '.$cfg['currency'].'</option>';

			sys::out($time);
		}

		$address = isset($_POST['address']) ? trim($_POST['address']) : sys::outjs(array('e' => 'Необходимо указать адрес игрового сервера'));

		if(sys::valid($address, 'other', $aValid['address']))
			sys::outjs(array('e' => 'Указанный адрес имеет неверный формат'));

		$sql->query('SELECT `id`, `name` FROM `servers` WHERE `address`="'.$address.'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Игровой сервер не найден в базе'));

		$server = $sql->get();

		$sql->query('SELECT `active` FROM `privileges` WHERE `server`="'.$server['id'].'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Игровой сервер не предоставляет услуги'));

		$privilege = $sql->get();

		if(!$privilege['active'])
			sys::outjs(array('e' => 'Игровой сервер времено не предоставляет услуги'));

		$name = '';

		$sql->query('SELECT  `id`, `name` FROM `privileges_list` WHERE `server`="'.$server['id'].'" ORDER BY `id` ASC LIMIT 5');
		while($list = $sql->get())
			$name .= '<option value="'.$list['id'].'">'.$list['name'].'</option>';

		$sql->query('SELECT  `data` FROM `privileges_list` WHERE `server`="'.$server['id'].'" ORDER BY `id` ASC LIMIT 1');
		$list = $sql->get();

		$time = '';

		$data = sys::b64djs($list['data']);

		if(isset($data[0]))
		{
			$time = '<option value="0">Навсегда / '.$data[0].' '.$cfg['currency'].'</option>';

			unset($data[0]);
		}

		foreach($data as $days => $price)
			$time .= '<option value="'.$days.'">'.$days.' '.sys::day($time).' / '.$price.' '.$cfg['currency'].'</option>';

		$html->get('form', 'sections/services/privileges');

			$html->set('home', $cfg['http']);
			$html->set('name', $server['name']);
			$html->set('address', $address);
			$html->set('services', $name);
			$html->set('time', $time);

		$html->pack('form');

		sys::outjs(array('s' => $html->arr['form']));
	}

	$html->get('index', 'sections/services/privileges');

	$html->pack('main');
?>