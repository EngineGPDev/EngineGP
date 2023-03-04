<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$nmch = 'reply_help_'.$user['id'];
        
	// Проверка сессии
	if($mcache->get($nmch))
		sys::outjs(array('e' => sys::text('other', 'mcache')), $nmch);
        
	// Создание сессии
	$mcache->set($nmch, 1, false, 10);

	if($id)
	{
		if(in_array($user['group'], array('admin', 'support')))
			$sql->query('SELECT `user` FROM `help` WHERE `id`="'.$id.'" AND `close`="0" LIMIT 1');
		else
			$sql->query('SELECT `user` FROM `help` WHERE `id`="'.$id.'" AND `close`="0" AND `user`="'.$user['id'].'" LIMIT 1');

		if(!$sql->num())
			sys::outjs(array('с' => 'Вопрос не открыт чтобы вести диалог.'), $nmch);

		$help = $sql->get();
	}else
		sys::outjs(array('e' => 'Вопрос не найден в базе.'), $nmch);

	$aData = array();

	$aData['text'] = isset($_POST['text']) ? $_POST['text'] : sys::outjs(array('e' => 'Сообщение не найдено.'), $nmch);
	$aData['images'] = isset($_POST['img']) ? $_POST['img'] : array();

	$aData['img'] = array();

	// Проверка сообщения
	if(iconv_strlen($aData['text'], 'UTF-8') < 2 || iconv_strlen(str_replace(array(' ', "\t", "\n"),'', $aData['text']), 'UTF-8') > 1000)
		sys::outjs(array('e' => 'Длина сообщения не должна быть менее 2 и не превышать 1000 символов.'), $nmch);

	include(LIB.'help.php');

	// Обработка сообщения
	$aData['text'] = help::text($aData['text']);

	// Проверка изображений
	if(is_array($aData['images']) AND count($aData['images']))
	{
		foreach($aData['images'] as $img)
		{
			$key = explode('.', $img);

			if(!is_array($key) || sys::valid($key[0], 'md5') || !in_array($key[1], array('png', 'gif', 'jpg', 'bmp')))
				continue;

			$sql->query('SELECT `id` FROM `help_upload` WHERE `name`="'.$img.'" LIMIT 1');
			if(!$sql->num())
				continue;

			$image = $sql->get();

			$sql->query('UPDATE `help_upload` set `status`="1" WHERE `id`="'.$image['id'].'" LIMIT 1');

			$aData['img'][] = $img;
		}
	}

	// Система контроля спама
	if($user['group'] == 'user')
	{
		$i = 0;
		$n = 3;
		$sql->query('SELECT `user` FROM `help_dialogs` WHERE `help`="'.$id.'" ORDER BY `id` DESC LIMIT 3');
		while($msg = $sql->get())
		{
			if(!$i AND !$msg['user'])
				sys::outjs(array('i' => 'Пожалуйста, дождитесь ответа технической поддержки.'), $nmch);

			$i+=1;

			if($msg['user'] == $help['user'])
				$n-=1;
		}

		if(!$n)
		{
			$sql->query('INSERT INTO `help_dialogs` set `help`="'.$id.'", `user`="0", `text`="Пожалуйста, дождитесь ответа технической поддержки.", `img`="", `time`="'.$start_point.'"');

			sys::outjs(array('i' => 'Пожалуйста, дождитесь ответа технической поддержки.'), $nmch);
		}
	}

	$sql->query('SELECT `text` FROM `help_dialogs` WHERE `help`="'.$id.'" ORDER BY `id` DESC LIMIT 1');
	$msg = $sql->get();

	if(md5($msg['text']) == md5($aData['text']))
		sys::outjs(array('e' => 'Такое сообщение уже отправлено.'), $nmch);

	$sql->query('INSERT INTO `help_dialogs` set '
		.'`help`="'.$id.'",'
		.'`user`="'.$user['id'].'",'
		.'`text`="'.$aData['text'].'",'
		.'`img`="'.sys::b64js($aData['img']).'",'
		.'`time`="'.$start_point.'"');

	if($user['group'] != 'user')
		$sql->query('UPDATE `help` set `status`="0" WHERE `id`="'.$id.'" LIMIT 1');
	else{
		$sql->query('UPDATE `help` set `status`="1" WHERE `id`="'.$id.'" LIMIT 1');
		$sql->query('UPDATE `help` set `notice_admin`="2" WHERE `id`="'.$id.'" LIMIT 1');
	}

	sys::outjs(array('s' => 'ok'), $nmch);
?>