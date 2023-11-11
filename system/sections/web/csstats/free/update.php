<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	switch($aWebInstall[$server['game']][$url['subsection']])
	{
		case 'server':
			$sql->query('SELECT `id`, `uid`, `desing`, `domain`, `update` FROM `web` WHERE `server`="'.$id.'" LIMIT 1');

			break;

		case 'user':
			$sql->query('SELECT `id`, `uid`, `desing`, `domain`, `update` FROM `web` WHERE `user`="'.$server['user'].'" LIMIT 1');

			break;

		case 'unit':
			$sql->query('SELECT `id`, `uid`, `desing`, `domain`, `update` FROM `web` WHERE `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');

			break;
	}

	if(!$sql->num())
		sys::outjs(array('e' => 'Дополнительная услуга не установлена.'), $nmch);

	$web = $sql->get();

	// Проверка времени последнего обновления
	include(LIB.'games/games.php');

	$upd = $web['update']+86400;

	if($upd > $start_point)
		sys::outjs(array('e' => 'Для повторного обновления должно пройти: '.games::date('max', $upd)));

	include(LIB.'ssh.php');

	if($aWebUnit['unit'][$url['subsection']] == 'local')
	{
		$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
		$unit = $sql->get();
	}else{
		$unit = array(
			'address' => $aWebUnit['address'],
			'passwd' => $aWebUnit['passwd'],
		);
	}

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

	$install = $aWebUnit['install'][$aWebUnit['unit'][$url['subsection']]][$url['subsection']].$web['domain'];

	$path = $aWebUnit['path'][$aWebUnit['unit'][$url['subsection']]][$url['subsection']].$web['desing'];

	$ssh->set('cat '.$install.'/include/db.config.inc.php > '.$path.'/include/db.config.inc.php;'
		.'cd '.$install.' && sudo -u web'.$web['uid'].' screen -dmS u_w_'.$web['uid'].' sh -c "YES | cp -rf '.$path.'/. .; '.$aWebChmod[$url['subsection']].'"');

	$sql->query('UPDATE `web` set `update`="'.$start_point.'" WHERE `id`="'.$web['id'].'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>