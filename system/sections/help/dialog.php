<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Отправка сообщения / Удаление сообщения
	if(isset($url['action']) AND in_array($url['action'], array('reply', 'remove', 'read', 'write')))
		include(SEC.'help/action/'.$url['action'].'.php');

	if(!$id)
		sys::back($cfg['http'].'help/section/open');

	if(in_array($user['group'], array('admin', 'support')))
		$sql->query('SELECT `type`, `service`, `status`, `date`, `close` FROM `help` WHERE `id`="'.$id.'" LIMIT 1');
	else
		$sql->query('SELECT `type`, `service`, `status`, `date`, `close` FROM `help` WHERE `id`="'.$id.'" AND `user`="'.$user['id'].'" LIMIT 1');

	if(!$sql->num())
		sys::back($cfg['http'].'help/section/open');

	$help = $sql->get();

	// Смена статуса вопроса на "Прочитан"
	if($user['group'] == 'user' AND !$help['status'])
	{
		$sql->query('UPDATE `help` set `status`="2", `notice`="1" WHERE `id`="'.$id.'" LIMIT 1');
		$help['status'] = 2;
	}

	$aGroup = array(
		'admin' => 'Администратор',
		'support' => 'Техническая поддержка',
		'user' => 'Клиент'
	);

	include(LIB.'help.php');
	include(LIB.'users.php');

	$aSender = array();

	$dialogs = $sql->query('SELECT `id`, `user`, `text`, `img`, `time` FROM `help_dialogs` WHERE `help`="'.$id.'" ORDER BY `id` DESC LIMIT 50');
	while($dialog = $sql->get($dialogs))
	{
		unset($html->arr['attachment']);

		$images = sys::b64djs($dialog['img']);

		if(is_array($images))
			foreach($images as $img)
			{
				$html->get('attachment', 'sections/help/dialog');

				   $html->set('img', $img);
				   $html->set('home', $cfg['http']);

				$html->pack('attachment');
			}

		$html->get('msg', 'sections/help/dialog');

			if($user['id'] != $dialog['user'])
			{
				if(!$dialog['user'])
					$html->set('sender', 'Автоматическое сообщение');
				else{
					if(isset($aSender[$dialog['user']]))
						$html->set('sender', $aSender[$dialog['user']]);
					else{
						switch($iHelp)
						{
							case 1:
								$sql->query('SELECT `name`, `group`, `support_info` FROM `users` WHERE `id`="'.$dialog['user'].'" LIMIT 1');
								$us = $sql->get();

								if($us['support_info'] != '')
									$aSender[$dialog['user']] = $us['name'].' ('.$us['support_info'].')';
								else
									$aSender[$dialog['user']] = $us['name'].' ('.$aGroup[$us['group']].')';

								break;
							case 2:
								$sql->query('SELECT `login`, `group`, `support_info` FROM `users` WHERE `id`="'.$dialog['user'].'" LIMIT 1');
								$us = $sql->get();

								if($us['support_info'] != '')
									$aSender[$dialog['user']] = $us['login'].' ('.$us['support_info'].')';
								else
									$aSender[$dialog['user']] = $us['login'].' ('.$aGroup[$us['group']].')';

								break;
							case 3:
								$sql->query('SELECT `mail`, `group`, `support_info` FROM `users` WHERE `id`="'.$dialog['user'].'" LIMIT 1');
								$us = $sql->get();

								if($us['support_info'] != '')
									$aSender[$dialog['user']] = $us['mail'].' ('.$us['support_info'].')';
								else
									$aSender[$dialog['user']] = $us['mail'].' ('.$aGroup[$us['group']].')';

								break;
							default:
								$sql->query('SELECT `name`, `patronymic`, `group`, `support_info` FROM `users` WHERE `id`="'.$dialog['user'].'" LIMIT 1');
								$us = $sql->get();

								if($us['support_info'] != '')
									$aSender[$dialog['user']] = $us['name'].' '.$us['patronymic'].' ('.$us['support_info'].')';
								else
									$aSender[$dialog['user']] = $us['name'].' '.$us['patronymic'].' ('.$aGroup[$us['group']].')';
						}

						$html->set('sender', $aSender[$dialog['user']]);
					}
				}
			}else
				$html->set('sender', 'Я');

			$html->set('id', $dialog['id']);
			$html->set('uid', $dialog['user']);
			$html->set('help', $id);
			$html->set('home', $cfg['http']);
			$html->set('ava', users::ava($dialog['user']));
			$html->set('text', $dialog['text']);

			if($tHelp)
				$html->set('time', $dialog['time'] < ($start_point-600) ? sys::today($dialog['time']) : help::ago($dialog['time']));
			else
				$html->set('time', sys::today($dialog['time']).' '.help::ago($dialog['time'], true));

			if(isset($html->arr['attachment']))
			{
				$html->set('img', $html->arr['attachment']);
				$html->unit('img', 1);
			}else
				$html->unit('img');

			if($user['group'] == 'admin')
				$html->unit('admin', 1);
			else
				$html->unit('admin');

		$html->pack('dialog');
	}

	// Массив статусов вопроса
	$status = array(
		0 => 'Есть ответ',
		1 => 'Ожидается ответ',
		2 => 'Прочитан'
	);

	if(isset($url['ajax']))
		sys::outjs(array('dialog' => (isset($html->arr['dialog']) ? $html->arr['dialog'] : ''), 'status' => ($help['close'] ? 'Вопрос решен' : $status[$help['status']])));

	// Краткая информация вопроса
	switch($help['type'])
	{
		case 'server':
			$sql->query('SELECT `address` FROM `servers` WHERE `id`="'.$help['service'].'" LIMIT 1');
			if(!$sql->num())
				$service = 'Игровой сервер: #'.$help['service'].' (не найден)';
			else{
				$ser = $sql->get();
				$service = '<a href="'.$cfg['http'].'servers/id/'.$help['service'].'" target="_blank"><u>Игровой сервер: #'.$help['service'].' '.$ser['address'].'</u></a>';
			}

		break;

		case 'hosting':
			$service = '<a href="'.$cfg['http'].'hosting/id/'.$help['service'].'" target="_blank"><u>Виртуальных хостинг: #'.$help['service'].'</u></a>';

		break;

		default:
			$service = 'Вопрос без определенной услуги';
	}

	$html->get('dialog', 'sections/help');

       $html->set('id', $id);
       $html->set('date', sys::today($help['date']));
       $html->set('status', $help['close'] ? 'Вопрос решен' : $status[$help['status']]);
       $html->set('service', $service);
       $html->set('dialog', isset($html->arr['dialog']) ? $html->arr['dialog'] : '');

	   if($user['group'] == 'user')
	   {
		   $html->unit('!user');
		   $html->unit('user', 1);
	   }else{
		   $html->unit('!user', 1);
		   $html->unit('user');
	   }

	   if($help['close'])
	   {
		   $html->unit('open');
		   $html->unit('close', 1);
	   }else{
		   $html->unit('open', 1);
		   $html->unit('close');
	   }

    $html->pack('main');
?>