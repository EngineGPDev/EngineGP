<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	$pid = isset($url['plugin']) ? sys::int($url['plugin']) : exit;

	// Проверка установки плагина
	$sql->query('SELECT `id`, `upd` FROM `plugins_install` WHERE `server`="'.$id.'" AND `plugin`="'.$pid.'" LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('e' => 'Данный плагин не установлен'));

	$plugin = $sql->get();

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	if(!isset($ssh))
		include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	// Директория игр. сервера
	$dir = $tarif['install'].$server['uid'].'/';

	// Имя исполняемого файла
	if($plugin['upd'])
	{
		$qsql = 'WHERE `update`="'.$plugin['upd'].'" ORDER BY `id` ASC';
		$frm = 'u'.$plugin['upd'];
	}else{
		$qsql = 'WHERE `plugin`="'.$pid.'" AND `update`="0" ORDER BY `id` ASC';
		$frm = $pid;
	}

	// Удаление и установка файлов
	$ssh->set('cd '.$dir.' && screen -dmS delete_upd_'.$start_point.' '
		.'sudo -u server'.$server['uid'].' sh -c "'
		.'wget --no-check-certificate '.$cfg['plugins'].'delete/'.$frm.'.rm && '
		.'chmod 755 '.$frm.'.rm; ./'.$frm.'.rm; rm '.$frm.'.rm"');

	include(LIB.'games/plugins.php');

	// Удаление добавленного при установке текста в файлах
	$sql->query('SELECT `text`, `file` FROM `plugins_write` '.$qsql);
	while($clear = $sql->get())
		plugins::clear($clear, $server['uid'], $dir);

	unset($clear);

	// Добавление текста при удалении в файлы
	$sql->query('SELECT `text`, `file`, `top` FROM `plugins_write_del` '.$qsql);
	while($write = $sql->get())
		plugins::write($write, $server['uid'], $dir);

	// Удаление записи установленного плагина в базе
	$sql->query('DELETE FROM `plugins_install` WHERE `server`="'.$id.'" AND `plugin`="'.$pid.'"');

	// Очистка кеша
	$mcache->delete('server_plugins_'.$id);

	if($plugin['upd'])
		$sql->query('SELECT `install` FROM `plugins_delete_ins` WHERE `update`="'.$plugin['upd'].'" LIMIT 1');
	else
		$sql->query('SELECT `install` FROM `plugins_delete_ins` WHERE `plugin`="'.$pid.'" AND `update`="0" LIMIT 1');

	if($sql->num())
	{
		$ins = $sql->get();

		$sql->query('SELECT `name` FROM `plugins` WHERE `id`="'.$ins['install'].'" LIMIT 1');
		if($sql->num())
		{
			$plugin = $sql->get();

			sys::outjs(array('i' => $ins['install'], 'pname' => $plugin['name']), $nmch);
		}
	}

	sys::outjs(array('s' => 'ok'), $nmch);
?>