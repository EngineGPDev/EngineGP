<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Закрытие / Удаление вопроса
	if(isset($url['action']) AND in_array($url['action'], array('open', 'delete')))
		include(SEC.'help/action/'.$url['action'].'.php');

	if(in_array($user['group'], array('admin', 'support')))
		$sql->query('SELECT `id`, `user`, `type`, `service`, `date`, `time` FROM `help` WHERE `close`="1"');
	else
		$sql->query('SELECT `id`, `type`, `service`, `date`, `time` FROM `help` WHERE `user`="'.$user['id'].'" AND `close`="1"');

	$aPage = sys::page($page, $sql->num(), 20);

	sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'help/section/close');

	if(in_array($user['group'], array('admin', 'support')))
		$helps = $sql->query('SELECT `id`, `user`, `type`, `service`, `date`, `time` FROM `help` WHERE `close`="1" ORDER BY `id` DESC LIMIT '.$aPage['num'].', 20');
	else
		$helps = $sql->query('SELECT `id`, `type`, `service`, `date`, `time` FROM `help` WHERE `user`="'.$user['id'].'" AND `close`="1" ORDER BY `id` DESC LIMIT '.$aPage['num'].', 20');

	// Массив пользователей
	$uArr = array();

	while($help = $sql->get($helps))
	{
		// Создатель вопроса
		if(in_array($user['group'], array('admin', 'support')) AND !isset($uArr[$help['user']]))
		{
			$sql->query('SELECT `login` FROM `users` WHERE `id`="'.$help['user'].'" LIMIT 1');

			if(!$sql->num())
				$uArr[$help['user']] = 'Пользователь удален';
			else{
				$us = $sql->get();
				$uArr[$help['user']] = $us['login'];
			}
		}

		// Краткая информация вопроса
		switch($help['type'])
		{
			case 'server':
				$sql->query('SELECT `address` FROM `servers` WHERE `id`="'.$help['service'].'" LIMIT 1');
				if(!$sql->num())
					$name = 'Игровой сервер: #'.$help['service'].' (не найден)';
				else{
					$ser = $sql->get();
					$name = 'Игровой сервер: #'.$help['service'].' '.$ser['address'];
				}

			break;

			case 'hosting':
				$name = 'Виртуальных хостинг: #'.$help['service'];

			break;

			default:
				$name = 'Вопрос без определенной услуги';
		}

		$html->get('question', 'sections/help/close');

			$html->set('id', $help['id']);

			if(array_key_exists('user', $help))
			{
				$html->set('uid', $help['user']);
				$html->set('login', $uArr[$help['user']]);
			}

			$html->set('name', $name);
			$html->set('status', 'Вопрос решен');
			$html->set('date', sys::today($help['date']));
			$html->set('time', sys::today($help['time']));

		$html->pack('question');
	}

	$html->get('close', 'sections/help');

        $html->set('question', isset($html->arr['question']) ? $html->arr['question'] : '');

        $html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

    $html->pack('main');

	if(!in_array($user['group'], array('admin', 'support')))
	{
		$html->unitall('main', 'user', 1);
		$html->unitall('main', 'support');
	}else{
		$html->unitall('main', 'user');
		$html->unitall('main', 'support', 1);
	}

	if($user['group'] == 'admin')
		$html->unitall('main', 'admin', 1);
	else
		$html->unitall('main', 'admin');
?>