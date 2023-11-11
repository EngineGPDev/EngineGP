<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	if($user['group'] != 'admin')
		sys::outjs(array('i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'), $nmch);

	switch($aWebInstall[$server['game']][$url['subsection']])
	{
		case 'server':
			$sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `server`="'.$id.'" LIMIT 1');

			break;

		case 'user':
			$sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" LIMIT 1');

			break;

		case 'unit':
			$sql->query('SELECT `id`, `uid`, `unit`, `login` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');

			break;
	}

	if(!$sql->num())
		sys::outjs(array('e' => 'Дополнительная услуга не установлена.'), $nmch);

	$web = $sql->get();

	if($aWebUnit['unit'][$url['subsection']] == 'local')
	{
		$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$web['unit'].'" LIMIT 1');
		$unit = $sql->get();
	}else{
		$unit = array(
			'address' => $aWebUnit['address'],
			'passwd' => $aWebUnit['passwd'],
		);
	}

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

	$ssh->set("mysql --login-path=local -e \"DROP DATABASE IF EXISTS ".$web['login']."; DROP USER ".$web['login']."\"");

	$sql->query('DELETE FROM `web` WHERE `id`="'.$web['id'].'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>