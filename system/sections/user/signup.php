<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Проверка на авторизацию
	sys::auth();

	// Генерация новой капчи
	if(isset($url['captcha']))
		sys::captcha('signup', $uip);

	$aData = array();

	// Сбор данных из $_POST в $aData
	if(isset($_POST['mail']))
	{
		foreach($aSignup['input'] as $name => $add)
		{
			if(!$add)
				continue;

			$aData[$name] = isset($_POST[$name]) ? trim($_POST[$name]) : '';
		}
	}

	// Регистрация
	if($go)
	{
		$nmch = 'go_signup_'.$uip;

		if($mcache->get($nmch))
			sys::outjs(array('e' => sys::text('other', 'mcache')), $nmch);

		$mcache->set($nmch, 1, false, 15);

		// Проверка капчи
		if(!isset($_POST['captcha']) || sys::captcha_check('signup', $uip, $_POST['captcha']))
			sys::outjs(array('e' => sys::text('other', 'captcha')), $nmch);

		// Проверка входных данных
		foreach($aData as $input => $val)
		{
			// Если не заполнено поле
			if($val == '')
				sys::outjs(array('e' => sys::text('input', 'all')), $nmch);

			// Проверка данных на валидность
			if(sys::valid($val, 'other', $aValid[$input]))
				sys::outjs(array('e' => sys::text('input', $input.'_valid')), $nmch);
		}

		// Проверка логина на занятость
		if(isset($aData['login']))
		{
			$sql->query('SELECT `id` FROM `users` WHERE `login`="'.$aData['login'].'" LIMIT 1');
			if($sql->num())
				sys::outjs(array('e' => sys::text('input', 'login_use')), $nmch);
		}

		if(!isset($aData['mail']))
			sys::outjs(array('e' => sys::text('input', 'mail_valid')), $nmch);

		// Проверка почты на занятость
		$sql->query('SELECT `id` FROM `users` WHERE `mail`="'.$aData['mail'].'" LIMIT 1');
		if($sql->num())
			sys::outjs(array('e' => sys::text('input', 'mail_use')), $nmch);

		// Проверка телефона на занятость
		if(isset($aData['phone']))
		{
			$sql->query('SELECT `id` FROM `users` WHERE `phone`="'.$aData['phone'].'" LIMIT 1');
			if($sql->num())
				sys::outjs(array('e' => sys::text('input', 'phone_use')), $nmch);
		}

		// Проверка контактов на занятость
		if(isset($aData['contacts']))
		{
			$sql->query('SELECT `id` FROM `users` WHERE `contacts`="'.$aData['contacts'].'" LIMIT 1');
			if($sql->num())
				sys::outjs(array('e' => sys::text('input', 'use_contacts')), $nmch);
		}

		// Проверка почты на подачу регистрации
		$sql->query('SELECT `id`, `key` FROM `signup` WHERE `mail`="'.$aData['mail'].'" LIMIT 1');
		if($sql->num())
		{
			$signup = $sql->get();
			$sql->query('UPDATE `signup` set `date`="'.$start_point.'" WHERE `id`="'.$signup['id'].'" LIMIT 1');

			// Повторная отправка письма на почту
			sys::mail(
				'Регистрация',
				sys::updtext(
					sys::text('mail', 'signup'),
					array(
						'site' => $cfg['name'],
						'url' => $cfg['http'].'user/section/signup/confirm/'.$signup['key']
					)
				),
				$aData['mail']
			);
			sys::outjs(array('s' => sys::text('output', 'remail'), 'mail' => sys::mail_domain($aData['mail'])), $nmch);
		}

		// Генерация ключа
		$key = sys::key('signup_'.$uip);

		$data = sys::b64js($aData);

		// Запись данных в базу
		$sql->query('INSERT INTO `signup` set `mail`="'.$aData['mail'].'", `key`="'.$key.'", `data`="'.$data.'", `date`="'.$start_point.'"');

		// Отправка сообщения на почту
		if(sys::mail('Регистрация', sys::updtext(sys::text('mail', 'signup'), array('site' => $cfg['name'], 'url' => $cfg['http'].'user/section/signup/confirm/'.$key)), $aData['mail']))
			sys::outjs(array('s' => sys::text('output', 'mail'), 'mail' => sys::mail_domain($aData['mail'])), $nmch);

		// Выхлоп: не удалось отправить письмо
		sys::outjs(array('e' => sys::text('error', 'mail')), $nmch);
	}

	// Завершение регистрации
	if(isset($url['confirm']) && !sys::valid($url['confirm'], 'md5'))
	{
		$sql->query('SELECT `id`, `data` FROM `signup` WHERE `key`="'.$url['confirm'].'" LIMIT 1');
		if($sql->num())
		{
			$signup = $sql->get();

			$aData = sys::b64djs($signup['data']);

			foreach($aSignup['input'] as $name => $add)
				$aNData[$name] = isset($aData[$name]) ? $aData[$name] : '';

			unset($aData);

			// Если регистрация без указания логина
			if(empty($aNData['login']))
			{
				$lchar = false;

				while(1)
				{
					$aNData['login'] = sys::login($aNData['mail'], $lchar);

					$sql->query('SELECT `id` FROM `users` WHERE `login`="'.$aNData['login'].'" LIMIT 1');
					if(!$sql->num())
						break;

					$lchar = true;
				}
			}

			// Если регистрация без указания пароля
			if(empty($aNData['passwd']))
				$aNData['passwd'] = sys::passwd(10);

			// Реферал
			if(isset($_COOKIE['part']))
				$part = ', `part`="'.sys::int($_COOKIE['part']).'"';

			// Запись данных в базу
			$sql->query('INSERT INTO `users` set '
				.'`login`="'.$aNData['login'].'",'
				.'`passwd`="'.sys::passwdkey($aNData['passwd']).'",'
				.'`mail`="'.$aNData['mail'].'",'
				.'`name`="'.$aNData['name'].'",'
				.'`lastname`="'.$aNData['lastname'].'",'
				.'`patronymic`="'.$aNData['patronymic'].'",'
				.'`phone`="'.$aNData['phone'].'",'
				.'`contacts`="'.$aNData['contacts'].'",'
				.'`balance`="0", `group`="user", `date`="'.$start_point.'"'.$part);

			$sql->query('DELETE FROM `signup` WHERE `id`="'.$signup['id'].'" LIMIT 1');

			// Отправка сообщения на почту
			if(sys::mail('Завершение регистрации', sys::updtext(sys::text('mail', 'signup_end'), array('site' => $cfg['name'], 'login' => $aNData['login'], 'passwd' => $aNData['passwd'])), $aNData['mail']))				
				sys::outhtml(sys::text('output', 'signup'), 5, 'http://'.sys::mail_domain($aNData['mail']));

			// Выхлоп: не удалось отправить письмо
			sys::outjs(array('e' => sys::text('error', 'mail')), $nmch);
		}

		sys::outhtml(sys::text('error', 'signup'), 5);
	}

	// Генерация формы
	foreach($aSignup['input'] as $name => $add)
	{
		if(!$add)
			continue;

		$html->get('signup', 'sections/user/inputs');
			$html->set('name', $name);
			$html->set('info', $aSignup['info'][$name]);
			$html->set('type', $aSignup['type'][$name]);
			$html->set('placeholder', $aSignup['placeholder'][$name]);
		$html->pack('inputs');
	}

	$html->get('signup', 'sections/user');

		$inputsjs = '';

		foreach($aSignup['input'] as $name => $add)
		{
			if(!$add)
				continue;

			$inputsjs .= '"'.$name.'",';
		}

		$html->set('inputs', $html->arr['inputs'], true);
		$html->set('inputsjs', $inputsjs);

	$html->pack('main');
?>