<?php

	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Парсинг адреса
	$url = is_array(sys::url()) ? sys::url() : array();
	$route = sys::url(false);
	$section = isset($url['section']) ? $url['section'] : false;

	$id = array_key_exists('id', $url) ? sys::int($url['id']) : false;
	$go = array_key_exists('go', $url);
	$page = array_key_exists('page', $url) ? sys::int($url['page']) : 1;
	$route = $route == '' ? 'index' : $route;

	// Реферал
	if(isset($_GET['account']))
		sys::cookie('part', sys::int($_GET['account']), 10);

	$auth = false;

	// Проверка cookie на авторизацию
	$aAuth = array();

	$aAuth['login'] = isset($_COOKIE['egp_login']) ? $_COOKIE['egp_login'] : '';
	$aAuth['passwd'] = isset($_COOKIE['egp_passwd']) ? $_COOKIE['egp_passwd'] : '';
	$aAuth['authkeycheck'] = isset($_COOKIE['egp_authkeycheck']) ? $_COOKIE['egp_authkeycheck'] : '';

	$authkey = md5($aAuth['login'].$uip.$aAuth['passwd']);
	$userkey = md5($aAuth['login'].$authkey.$aAuth['passwd']);

	if(!in_array('', $aAuth) && $authkey == $aAuth['authkeycheck'])
	{
		$users = $mcache->get('users_auth');

		$user = isset($users[$userkey]) ? $users[$userkey] : 0;

		if(!$user)
		{
			if((!sys::valid($aAuth['login'], 'other', $aValid['login'])) && !sys::valid($aAuth['passwd'], 'md5'))
			{
				$sql->query('SELECT `id` FROM `users` WHERE `login`="'.$aAuth['login'].'" AND `passwd`="'.$aAuth['passwd'].'" LIMIT 1');
				if($sql->num())
				{
					$sql->query('SELECT `id`, `login`, `passwd`, `balance`, `group`, `level`, `time` FROM `users` WHERE `login`="'.$aAuth['login'].'" AND `passwd`="'.$aAuth['passwd'].'" LIMIT 1');
					$user = array_merge(array('authkey' => $authkey), $sql->get());

					$auth = 1;

					sys::users($users, $user, $authkey);
				}
			}

			if(!$auth)
			{
				sys::cookie('egp_login', 'quit', -1);
				sys::cookie('egp_passwd', 'quit', -1);
				sys::cookie('egp_authkeycheck', 'quit', -1);
			}
		}else{
			$sql->query('SELECT `balance`, `time` FROM `users` WHERE `id`="'.$user['id'].'" LIMIT 1');
			$user = array_merge($user, $sql->get());

			sys::user($user);

			$auth = 1;
		}
	}

	// Заголовок
	$title = '';

	// Навигация
	$html->nav($cfg['name'], $cfg['http']);

	include(DATA.'header.php');

	// Подключение файла
	if(in_array($route, $aRoute))
		include(ENG.$route.'.php');
	else
		include(ENG.'404.php');

	// Обновление ссылок
	if(isset($html->arr['main']))
	{
		$html->upd(
			array(
				'[home]',
				'[js]',
				'[css]',
				'[img]'
			),

			array(
				$cfg['http'],
				$cfg['http'].'template/js/',
				$cfg['http'].'template/css/',
				$cfg['http'].'template/images/'
			),
			'main'
		);
	}

	// Онлайн игроков (общее количество всех игроков)
	//$aop = $mcache->get('all_online_players'); //Если ваш хостинг чувствует себя плохо из за чрезмерной нагрузки от данного модуля, то включите кеширование, раскомментировав этот кусочек кода
	if($aop == '')
	{
		$sql->query('SELECT SUM(`online`) FROM `servers` WHERE `status`="working" OR `status`="change"');
		$sum = $sql->get();

		$aop = $sum['SUM(`online`)'];

		$mcache->set('all_online_players', $aop, false, 1);
	}

	// Заготовка выхлопа
	$html->get('all');
		$html->set('title', $title.' | '.$cfg['name']);
		$html->set('description', sys::head('description'));
		$html->set('keywords', sys::head('keywords'));
		$html->set('home', $cfg['http']);
		$html->set('js', $cfg['http'].'template/js/');
		$html->set('css', $cfg['http'].'template/css/');
		$html->set('img', $cfg['http'].'template/images/');
		$html->set('aop', $aop);
		$html->set('cur', $cfg['currency']);

		// Если авторизован
		if($auth)
		{
			$html->set('login', $user['login']);
			$html->set('balance', round($user['balance'], 2));
			$html->set('other_menu', isset($html->arr['vmenu']) ? $html->arr['vmenu'] : '');
		}else
			$html->set('other_menu', '');

		$html->set('nav', isset($html->arr['nav']) ? $html->arr['nav'] : '', true);
		$html->set('main', isset($html->arr['main']) ? $html->arr['main'] : '', true);

		$sql->query('SELECT `id`, `login`, `time` FROM `users` ORDER BY `id` ASC');
		$online = '<span style="padding:0 5px;">';
		while($staff = $sql->get())
		{
			if ($staff['time']+15 > $start_point) {
				$online .= $staff['login'].', ';
			}
			else {
				$online .= '';
			}
		}
		$online .= '</span>';
		$html->set('online_users', $online);
	$html->pack('all');

	// Блоки
	if($auth)
	{
		// Проверка наличия игрового сервера
		$servers = $sql->query('(SELECT `id` FROM `servers` WHERE `user`="'.$user['id'].'" LIMIT 1) UNION (SELECT `id` FROM `owners` WHERE `user`="'.$user['id'].'" LIMIT 1)');

		if($sql->num())
			$html->unitall('all', 'servers', 1, 1);
		else
			$html->unitall('all', 'servers', 0, 1);

		// Проверка наличия игрового сервера
		$servers = $sql->query('SELECT `id` FROM `control` WHERE `user`="'.$user['id'].'" LIMIT 1');

		if($sql->num())
			$html->unitall('all', 'control', 1);
		else
			$html->unitall('all', 'control', 0);

		$html->unitall('all', 'auth', 1, 1);
		$html->unitall('all', 'admin', $user['group'] == 'admin', 1);
		$html->unitall('all', 'support', $user['group'] == 'support', 1);
	}else{
		$html->unitall('all', 'auth', 0, 1);
		$html->unitall('all', 'servers', 0, 1);
		$html->unitall('all', 'control', 0, 1);
		$html->unitall('all', 'admin', 0, 1);
		$html->unitall('all', 'support', 0, 1);
	}
?>