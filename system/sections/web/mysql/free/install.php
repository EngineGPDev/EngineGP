<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Установка
	if($go)
	{
		if(!$aWeb[$server['game']][$url['subsection']])
			sys::outjs(array('e' => 'Дополнительная услуга недоступна для установки.'), $nmch);

		// Проверка на наличие уже установленной выбранной услуги
		switch($aWebInstall[$server['game']][$url['subsection']])
		{
			case 'server':
				$sql->query('SELECT `id` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `server`="'.$id.'" LIMIT 1');
				break;

			case 'user':
				$sql->query('SELECT `id` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" LIMIT 1');
				break;

			case 'unit':
				$sql->query('SELECT `id` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');
				break;
		}

		if($sql->num())
			sys::outjs(array('s' => 'ok'), $nmch);

		include(LIB.'ssh.php');

		if($aWebUnit['unit'][$url['subsection']] == 'local')
		{
			$sql->query('SELECT `address`, `passwd`, `domain` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			$pma = $unit['domain'];
		}else{
			$unit = array(
				'address' => $aWebUnit['address'],
				'passwd' => $aWebUnit['passwd'],
			);

			$pma = $aWebUnit['pma'];
		}

		if(!$ssh->auth($unit['passwd'], $unit['address']))
			sys::outjs(array('e' => sys::text('ssh', 'error')), $nmch);

		if(isset($_POST['passwd']))
		{
			// Если не указан пароль сгенерировать
			if($_POST['passwd'] == '')
				$passwd = sys::passwd($aWebParam[$url['subsection']]['passwd']);
			else{
				// Проверка длинны пароля
				if(!isset($_POST['passwd']{5}) || isset($_POST['passwd']{16}))
					sys::outjs(array('e' => 'Необходимо указать пароль длинной не менее 6-и символов и не более 16-и.'), $nmch);

				// Проверка валидности пароля
				if(sys::valid($_POST['passwd'], 'other', "/^[A-Za-z0-9]{6,16}$/"))
					sys::outjs(array('e' => 'Пароль должен состоять из букв a-z и цифр.'), $nmch);

				$passwd = $_POST['passwd'];
			}
		}else
			$passwd = sys::passwd($aWebParam[$url['subsection']]['passwd']);

		$sql->query('INSERT INTO `web` set `type`="'.$url['subsection'].'", `server`="'.$id.'", `user`="'.$server['user'].'", `unit`="'.$server['unit'].'", `config`=""');
		$wid = $sql->id();
		$uid = $wid+10000;

		// Данные
		$login = substr('sql_'.$wid.'_free', 0, 14);

		$sql_q = 'mysql --login-path=local -e "CREATE DATABASE '.$login.';'
			."CREATE USER '".$login."'@'%' IDENTIFIED BY '".$passwd."';"
			.'GRANT ALL PRIVILEGES ON '.$login.' . * TO \''.$login.'\'@\'%\';";';

		$ssh->set($sql_q);

		// Обновление данных
		$sql->query('UPDATE `web` set `uid`="'.$uid.'",'
			.'`domain`="'.$pma.'",'
			.'`passwd`="'.$passwd.'",'
			.'`login`="'.$login.'", `date`="'.$start_point.'" '
			.'WHERE `id`="'.$wid.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'), $nmch);
	}

	$html->nav('Установка '.$aWebname[$url['subsection']]);

	$html->get('install', 'sections/web/'.$url['subsection'].'/free');

		$html->set('id', $id);

	$html->pack('main');
?>