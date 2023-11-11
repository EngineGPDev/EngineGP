<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	$pid = isset($url['plugin']) ? sys::int($url['plugin']) : exit;

	$sql->query('SELECT `name`, `cfg`, `upd`, `incompatible`, `required`, `packs`, `price` FROM `plugins` WHERE `id`="'.$pid.'" AND `game`="'.$server['game'].'" LIMIT 1');

	if(!$sql->num())
		exit;

	$plugin = $sql->get();

	// Проверка установки плагина
	$sql->query('SELECT `id` FROM `control_plugins_install` WHERE `server`="'.$sid.'" AND `plugin`="'.$pid.'" LIMIT 1');
	if($sql->num())
		sys::outjs(array('e' => 'Данный плагин уже установлен'));

	$upd = false;

	// Если есть более поздняя версия плагина
	if($plugin['upd'])
	{
		$sql->query('SELECT `name`, `id`, `cfg`, `incompatible`, `required`, `packs`, `price` FROM `plugins_update` WHERE `plugin`="'.$pid.'" ORDER BY `id` DESC LIMIT 1');
		if($sql->num())
		{
			$plugin = array_merge($plugin, $sql->get());

			$upd = true;
		}
	}

	$buy = false;

	// Если платный плагин
	if($plugin['price'])
	{
		// Проверка покупки
		$sql->query('SELECT `id` FROM `control_plugins_buy` WHERE `plugin`="'.$pid.'" AND `server`="'.$sid.'" LIMIT 1');
		if($sql->num())
			$buy = true;
		else{
			// Проверка баланса
			if($user['balance'] < $plugin['price'])
				sys::outjs(array('e' => 'У вас не хватает '.(round($plugin['price']-$user['balance'], 2)).' '.$cfg['currency']), $name_mcache);
		}
	}

	// Проверка на доступность плагина к установленной на сервере сборке
	$packs = strpos($plugin['packs'], ':') ? explode(':',$plugin['packs']) : array($plugin['packs']);
	if(!in_array($server['pack'], $packs) AND $plugin['packs'] != 'all')
		exit;

	include(LIB.'control/plugins.php');

	// Проверка на наличие несовместимости с уже установленными плагинами
	plugins::incompatible($sid, $plugin['incompatible'], $nmch);

	// Проверка на наличие необходимых установленых плагинов для устанавливаемого дополнения
	plugins::required($sid, $plugin['required'], $nmch);

	$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$unit = $sql->get();

	if(!isset($ssh))
		include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

	if($upd)
	{
		$qsql = 'WHERE `update`="'.$plugin['id'].'" ORDER BY `id` ASC';
		$fzip = 'u'.$plugin['id'];
		$upd = $plugin['id'];
	}else{
		$qsql = 'WHERE `plugin`="'.$pid.'" AND `update`="0" ORDER BY `id` ASC';
		$fzip = $pid;
		$upd = 0;
	}

	// Директория игр. сервера
	$dir = '/servers/'.$server['uid'].'/';

	// Установка файлов на сервер
	$ssh->set('cd '.$dir.' && screen -dmS install_'.$start_point.' sudo -u server'.$server['uid'].' sh -c "'
		.'wget --no-check-certificate '.$cfg['plugins'].'install/'.$fzip.'.zip && unzip -o '.$fzip.'.zip; rm '.$fzip.'.zip;'
		.'find . -type d -exec chmod 700 {} \;;'
		.'find . -type f -exec chmod 600 {} \;;'
		.'chmod 500 '.params::$aFileGame[$server['game']].'"');

	// Удаление файлов
	$sql->query('SELECT `file` FROM `plugins_delete` '.$qsql);
	while($delete = $sql->get())
		$ssh->set('sudo -u server'.$server['uid'].' rm '.$dir.$delete['file']);

	// Удаление текста из файлов
	$sql->query('SELECT `text`, `file`, `regex` FROM `plugins_clear` '.$qsql);
	while($clear = $sql->get())
		plugins::clear($clear, $server['uid'], $dir);

	// Добавление текста в файлы
	$sql->query('SELECT `text`, `file`, `top` FROM `plugins_write` '.$qsql);
	while($write = $sql->get())
		plugins::write($write, $server['uid'], $dir);

	// Если платный плагин
	if(!$buy AND $plugin['price'])
	{
		$sql->query('UPDATE `users` set `balance`=`balance`-"'.$plugin['price'].'" WHERE `id`="'.$user['id'].'" LIMIT 1');

		$sql->query('INSERT INTO `control_plugins_buy` set `plugin`="'.$pid.'", `key`="'.md5(strip_tags($plugin['name'])).'", `server`="'.$sid.'", `price`="'.$plugin['price'].'", `time`="'.$start_point.'"');

		// Запись логов
		$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'ctrl_buy_plugin'),
			array('plugin' => strip_tags($plugin['name']), 'money' => $plugin['price'], 'id' => $sid)).'", `date`="'.$start_point.'", `type`="buy", `money`="'.$plugin['price'].'"');
	}

	// Запись данных в базу
	$sql->query('INSERT INTO `control_plugins_install` set `server`="'.$sid.'", `plugin`="'.$pid.'", `upd`="'.$upd.'", `time`="'.$start_point.'"');

	// Очистка кеша
	$mcache->delete('ctrl_server_plugins_'.$sid);

	if($plugin['cfg'])
		sys::outjs(array('s' => 'cfg'), $nmch);

	sys::outjs(array('s' => 'ok'), $nmch);
	
?>