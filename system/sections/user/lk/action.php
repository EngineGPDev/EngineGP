<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `mail`, `new_mail`, `confirm_mail`, `wmr`, `phone`, `confirm_phone`, `contacts` FROM `users` WHERE `id`="'.$user['id'].'" LIMIT 1');
    $user = array_merge($user, $sql->get());

	if($go)
    {
        $name_mcache = 'lk_'.$user['id'];

        // Проверка сессии
        if($mcache->get($name_mcache))
            sys::outjs(array('e' => $text['mcache']), $name_mcache);

        // Создание сессии
        $mcache->set($name_mcache, 1, false, 10);

		if(!isset($url['type']))
			exit;

		switch($url['type'])
		{
			case 'contacts':
				$contacts = isset($_POST['contacts']) ? $_POST['contacts'] : '';

				if($contacts != '')
				{
					if(sys::valid($contacts, 'other', $aValid['contacts']))
						sys::outjs(array('e' => sys::text('input', 'contacts_valid')), $name_mcache);
				}

				// Запись контактов в базу, если не совпадает с текущими данными
				if($contacts != $user['contacts'])
					$sql->query('UPDATE `users` set `contacts`="'.$contacts.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

				// Выхлоп удачного выполнения операции
				sys::outjs(array('s' => 'ok'), $name_mcache);

			case 'passwd':
				$passwd = isset($_POST['passwd']) ? $_POST['passwd'] : '';

				if(sys::valid($passwd, 'other', $aValid['passwd']))
					sys::outjs(array('e' => sys::text('input', 'passwd_valid')), $name_mcache);

				$passwd = sys::passwdkey($passwd);

				// Обновление пароля в базе, если он не совпадает с текущим
				if($auth_data['passwd'] != $passwd)
				{
					$sql->query('UPDATE `users` set `passwd`="'.$passwd.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

					// Обновление cookie
					sys::cookie('login', $user['login'], 14);
					sys::cookie('passwd', $passwd, 14);
					sys::cookie('authkeycheck', md5($user['login'].$_SERVER['REMOTE_ADDR'].$passwd), 14);
				}

				// Выхлоп удачного выполнения операции
				sys::outjs(array('s' => 'ok'), $name_mcache);

			case 'mail':
				$mail = isset($_POST['mail']) ? $_POST['mail'] : '';

				// Проверка введенной почты
				if(sys::valid($mail, 'other', $aValid['mail']))
					sys::outjs(array('e' => sys::text('input', 'mail_valid')), $name_mcache);

				if($mail == $user['mail'])
					sys::outjs(array('e' => sys::text('input', 'similar')), $name_mcache);

				// Проверка почты на занятость
				$sql->query('SELECT `id` FROM `users` WHERE `mail`="'.$mail.'" LIMIT 1');
				if($sql->num())
					sys::outjs(array('e' => sys::text('input', 'mail_use')), $name_mcache);

				// Генерация кода
				$key = sys::key('mail_change'.$user['id']);

				// Отправка письма на старую почту
				if(sys::mail('Смена почты', sys::updtext(sys::text('mail', 'change'), array('site' => $cfg['name'], 'url' => $cfg['http'].'user/section/lk/subsection/action/type/confirm_mail/confirm/'.$key.'/go/1')), $user['mail']))
				{
					$sql->query('UPDATE `users` set `new_mail`="'.$mail.'", `confirm_mail`="'.$key.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

					sys::outjs(array('s' => sys::text('output', 'oldmail'), 'mail' => sys::mail_domain($user['mail'])), $name_mcache);
				}

				// Выхлоп: неудалось отправить письмо
				sys::outjs(array('e' => sys::text('error', 'mail')), $name_mcache);

			case 'confirm_mail':
				$key = isset($url['confirm']) ? $url['confirm'] : '';

				if($key != $user['confirm_mail'])
					sys::outhtml(sys::text('output', 'confirm_key_error'), 4, $cfg['http'].'user/section/lk', $name_mcache);

				// Проверка почты на занятость
				$sql->query('SELECT `id` FROM `users` WHERE `mail`="'.$user['confirm_mail'].'" LIMIT 1');
				if($sql->num())
					sys::outhtml(sys::text('input', 'mail_use'), 4, $cfg['http'].'user/section/lk', $name_mcache);

				$sql->query('UPDATE `users` set `mail`="'.$user['new_mail'].'", `new_mail`="", `confirm_mail`="" WHERE `id`="'.$user['id'].'" LIMIT 1');

				// Выхлоп удачного выполнения операции
				sys::outhtml(sys::text('output', 'confirm_mail_done'), 4, $cfg['http'].'user/section/lk', $name_mcache);

			case 'phone':
				// Проверка, подтвержден ли номер
				if($user['confirm_phone'] == '1')
					sys::outjs(array('e' => sys::text('output', 'confirm_phone')), $name_mcache);

				$phone = isset($_POST['phone']) ? str_replace('+', '', trim($_POST['phone'])) : '';

				// Проверка введенного номера
				if($phone != '')
				{
					if(sys::valid($phone, 'other', $aValid['phone']))
						sys::outjs(array('e' => sys::text('input', 'phone_valid')), $name_mcache);
				}

				// Запись номера, если не совпадает с текущим
				if($phone != $user['phone'])
					$sql->query('UPDATE `users` set `phone`="'.$phone.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

				// Выхлоп удачного выполнения операции
				sys::outjs(array('s' => 'ok'), $name_mcache);

			case 'confirm_phone':
				// Проверка, подтвержден ли номер
				if($user['confirm_phone'] == '1')
					sys::outjs(array('e' => sys::text('output', 'confirm_phone_done')), $name_mcache);

				if($user['phone'] == '')
					sys::outjs(array('e' => sys::text('input', 'phone')), $name_mcache);

				// Проверка, отправлялось ли сообщение
				if(strlen($user['confirm_phone']) == 6)
					sys::outjs(array('s' => 'ok'), $name_mcache);

				// Генерация кода подтверждения
				$code = sys::smscode();
				
				// Отправка кода подтверждения на номер
				if(sys::sms('code: '.$code, $user['phone']))
				{
					$sql->query('UPDATE `users` set `confirm_phone`="'.$code.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

					sys::outjs(array('s' => 'ok'), $name_mcache);
				}

				// Выхлоп: неудалось отправить сообщение
				sys::outjs(array('e' => sys::text('output', 'confirm_phone_error')), $name_mcache);

			case 'confirm_phone_end':
				// Проверка, подтвержден ли номер
				if($user['confirm_phone'] == '1')
					sys::outjs(array('e' => sys::text('output', 'confirm_phone_done')), $name_mcache);

				if($user['phone'] == '')
					sys::outjs(array('e' => sys::text('input', 'phone')), $name_mcache);

				$code = isset($_POST['smscode']) ? sys::int($_POST['smscode']) : '';

				if($code != $user['confirm_phone'])
					sys::outjs(array('e' => sys::text('output', 'confirm_key_error')), $name_mcache);

				$sql->query('UPDATE `users` set `confirm_phone`="1" WHERE `id`="'.$user['id'].'" LIMIT 1');

				// Выхлоп удачного выполнения операции
				sys::outjs(array('s' => 'ok'), $name_mcache);

			case 'wmr':
				$wmr = isset($_POST['wmr']) ? $_POST['wmr'] : '';

				// Проверка наличия указанного кошелька
				 if(isset($user['wmr']{0}) AND in_array($user['wmr']{0}, array('R', 'Z', 'U')))
					sys::outjs(array('e' => sys::text('input', 'wmr_confirm')), $name_mcache);

				if(sys::valid($wmr, 'wm'))
					sys::outjs(array('e' => sys::text('input', 'wmr_valid')), $name_mcache);

				// Обновление кошелька в базе
				$sql->query('UPDATE `users` set `wmr`="'.$wmr.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

				// Выхлоп удачного выполнения операции
				sys::outjs(array('s' => 'ok'), $name_mcache);
		}
    }
?>