<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Редактируемый файл
	$file = isset($url['file']) ? $url['file'] : sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/settings');

	include(DATA.'filedits.php');

	// Проверка наличия в конфиге
	if(!in_array($file, $aEdits[$server['game']]['all']['files']))
		sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/settings');

    $html->nav('Редактирование файла: '.$file);

	$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$unit = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
	{
		if($go)
			sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

		sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/settings');
	}

	// Полный путь файла
	$path = '/servers/'.$server['uid'].'/'.$aEdits[$server['game']]['all']['path'][$file].$file;

	if($go)
	{
		$data = isset($_POST['data']) ? $_POST['data'] : '';

		$temp = sys::temp($data);

		// Отправление файла на сервер
		$ssh->setfile($temp, $path, 0644);

		// Смена владельца/группы файла
		$ssh->set('chown server'.$server['uid'].':servers '.$path);

		unlink($temp);

		sys::outjs(array('s' => 'ok'), $nmch);
	}

	$ssh->set('sudo -u server'.$server['uid'].' sh -c "touch '.$path.'; cat '.$path.'"');

	$html->get('file', 'sections/control/servers/games/settings');

		$html->set('id', $id);
		$html->set('server', $sid);
		$html->set('file', $file);
		$html->set('data', htmlspecialchars($ssh->get()));

	$html->pack('main');
?>