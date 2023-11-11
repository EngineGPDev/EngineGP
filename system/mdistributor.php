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

	// Подключение файла
	if(in_array($route, $amRoute))
		include(ENG.'megp/'.$route.'.php');
	else
		include(ENG.'megp/index.php');

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
				$cfg['http'].'template/megp/js/',
				$cfg['http'].'template/megp/css/',
				$cfg['http'].'template/megp/images/'
			),
			'main'
		);
	}

	// Заготовка выхлопа
	$html->get('all');
		$html->set('title', $title.' | '.$cfg['name']);
		$html->set('home', $cfg['http']);
		$html->set('js', $cfg['http'].'template/megp/js/');
		$html->set('css', $cfg['http'].'template/megp/css/');
		$html->set('img', $cfg['http'].'template/megp/images/');

		if($auth)
			$html->set('server_menu', isset($html->arr['vmenu']) ? $html->arr['vmenu'] : '');
		else
			$html->set('server_menu', '');

		$html->set('main', isset($html->arr['main']) ? $html->arr['main'] : '', true);
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
	}
?>