<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$fid = isset($url['file']) ? sys::int($url['file']) : sys::back($cfg['http'].'servers/id/'.$id.'/section/plugins');

	$sql->query('SELECT `plugin`, `update`, `file` FROM `plugins_config` WHERE `id`="'.$fid.'" LIMIT 1');

	if(!$sql->num())
		sys::back($cfg['http'].'servers/id/'.$id.'/section/plugins');

	$config = $sql->get();

	$sql->query('SELECT `id` FROM `plugins_install` WHERE `server`="'.$id.'" AND `plugin`="'.$config['plugin'].'" LIMIT 1');

	if(!$sql->num())
		sys::back($cfg['http'].'servers/id/'.$id.'/section/plugins');

	// Если обновленный плагин
	if($config['update'])
		$sql->query('SELECT `name` FROM `plugins_update` WHERE `id`="'.$config['update'].'" LIMIT 1');
	else
		$sql->query('SELECT `name` FROM `plugins` WHERE `id`="'.$config['plugin'].'" LIMIT 1');

	$plugin = $sql->get();

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	if(!isset($ssh))
		include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
	{
		if($go)
			sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

		sys::back($cfg['http'].'servers/id/'.$id.'/section/settings');
	}

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	// Данные файла
	$file = explode('/', $config['file']);

	// Полный путь файла
	$path = $tarif['install'].$server['uid'].'/'.$config['file'];

	// Сохранение
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

	$html->nav('Плагины', $cfg['http'].'servers/id/'.$id.'/section/plugins');
	$html->nav($plugin['name'], $cfg['http'].'servers/id/'.$id.'/section/plugins/subsection/plugin/plugin/'.$config['plugin']);

	$html->get('config', 'sections/servers/games/plugins');

		$html->set('id', $id);
		$html->set('file', $fid);
		$html->set('plugin', $config['plugin']);
		$html->set('name', end($file));
		$html->set('data', htmlspecialchars($ssh->get()));

	$html->pack('main');

?>