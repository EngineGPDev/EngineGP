<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$nmch = 'create_help_'.$user['id'];

		// Проверка сессии
		if($mcache->get($nmch))
			sys::outjs(array('e' => $text['mcache']), $nmch);

		// Создание сессии
		$mcache->set($nmch, 1, false, 10);

		$aData = array();

		$aData['service'] = isset($_POST['service']) ? explode('_', $_POST['service']) : exit();
		$aData['title'] = isset($_POST['title']) ? strip_tags(trim($_POST['title'])) : '';
		$aData['text'] = isset($_POST['text']) ? $_POST['text'] : exit();
		$aData['images'] = isset($_POST['img']) ? $_POST['img'] : array();

		$aData['img'] = array();

		/*
			Проверка входных данных
		*/

		// Проверка услуги
		if(count($aData['service']) != 2)
		{
			if($aData['service'][0] != 'none')
				sys::outjs(array('e' => 'Необходимо выбрать услугу связанную с вопросом.'), $nmch);

			$aData['type'] = 'none';
			$aData['service'] = 0;
		}else{
			if(!in_array($aData['service'][0], array('server', 'hosting')))
				sys::outjs(array('e' => 'Необходимо выбрать услугу связанную с вопросом.'), $nmch);

			$aData['type'] = $aData['service'][0];
			$aData['service'] = sys::int($aData['service'][1]);

			switch($aData['type'])
			{
				case 'server':
					$sql->query('SELECT `id` FROM `servers` WHERE `id`="'.$aData['service'].'" AND `user`="'.$user['id'].'" LIMIT 1');
				break;

				case 'hosting':
					$sql->query('SELECT `id` FROM `hosting` WHERE `id`="'.$aData['service'].'" AND `user`="'.$user['id'].'" LIMIT 1');
			}

			if(!$sql->num())
				sys::outjs(array('e' => 'Выбранная услуга не найдена в базе.'), $nmch);

			// Защита от дублирования темы вопроса
			$sql->query('SELECT `id` FROM `help` WHERE `user`="'.$user['id'].'" AND `type`="'.$aData['type'].'" AND `service`="'.$aData['service'].'" AND `close`="0" LIMIT 1');
			if($sql->num())
				sys::outjs(array('e' => 'По выбранной услуге уже есть открытый диалог.'), $nmch);
		}

		// Проверка заголовка, если указан
		if(!empty($aData['title']))
		{
			if(iconv_strlen($aData['title'], 'UTF-8') < 3 || iconv_strlen($aData['title'], 'UTF-8') > 40)
				sys::outjs(array('e' => 'Длина загловка не должна быть менее 3 и не превышать 40 символов.'), $nmch);
		}

		// Проверка сообщения
		if(iconv_strlen($aData['text'], 'UTF-8') < 10 || iconv_strlen($aData['text'], 'UTF-8') > 1000)
			sys::outjs(array('e' => 'Длина сообщения не должна быть менее 10 и не превышать 1000 символов.'), $nmch);

		include(LIB.'help.php');

		// Обработка сообщения
		$aData['text'] = help::text($aData['text']);

		// Проверка изображений
		if(is_array($aData['images']) AND count($aData['images']))
		{
			foreach($aData['images'] as $img)
			{
				$key = explode('.', $img);

				if(!is_array($key) || sys::valid($key[0], 'md5') || !in_array($key[1], array('png', 'gif', 'jpg', 'jpeg','bmp')))
					continue;

				$sql->query('SELECT `id` FROM `help_upload` WHERE `name`="'.$img.'" LIMIT 1');
				if(!$sql->num())
					continue;

				$image = $sql->get();

				$sql->query('UPDATE `help_upload` set `status`="1" WHERE `id`="'.$image['id'].'" LIMIT 1');

				$aData['img'][] = $img;
			}
		}

		// Проверка открытых сообщений
		$sql->query('SELECT `id` FROM `help` WHERE `user`="'.$user['id'].'" AND `close`="0" LIMIT 3');
		if($sql->num() == 3)
			sys::outjs(array('e' => 'У вас уже открыто 3 вопроса, чтобы создать новый необходимо их закрыть.'), $nmch);

		$sql->query('INSERT INTO `help` set '
			.'`user`="'.$user['id'].'",'
			.'`type`="'.$aData['type'].'",'
			.'`service`="'.$aData['service'].'",'
			.'`status`="1",'
			.'`date`="'.$start_point.'",'
			.'`time`="'.$start_point.'",'
			.'`title`="'.htmlspecialchars($aData['title']).'",'
			.'`close`="0"');

		$help = $sql->id();

		$sql->query('INSERT INTO `help_dialogs` set '
			.'`help`="'.$help.'",'
			.'`user`="'.$user['id'].'",'
			.'`text`="'.$aData['text'].'",'
			.'`img`="'.sys::b64js($aData['img']).'",'
			.'`time`="'.$start_point.'"');

		sys::outjs(array('s' => $help), $nmch);
	}

	$services = '';

	$sql->query('SELECT `id`, `address` FROM `servers` WHERE `user`="'.$user['id'].'" LIMIT 10');
	while($server = $sql->get())
		$services .= '<option value="server_'.$server['id'].'">Игровой сервер #'.$server['id'].' ('.$server['address'].')</option>';

	$html->get('create', 'sections/help');

        $html->set('id', $user['id']);
        $html->set('services', $services);

    $html->pack('main');
?>