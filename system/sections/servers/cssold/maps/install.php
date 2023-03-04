<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	$sql->query('SELECT `unit`, `tarif`, `hdd` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = array_merge($server, $sql->get());

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	if(!isset($ssh))
		include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	$dir = $tarif['install'].$server['uid'].'/cstrike/';

	// Проверить наличие свободного места
	$ssh->set('cd '.$dir.' && du -ms');
	$hdd = ceil(sys::int($ssh->get())/($server['hdd']/100));
	$hdd = $hdd > 100 ? 100 : $hdd;
		
	if($hdd == 100)
		sys::outjs(array('e' => 'Невозможно выполнить установку, нет свободного места'), $nmch);

	// Массив переданных карт
	$in_aMaps = isset($_POST['maps']) ? $_POST['maps'] : array();

	// Обработка выборки
	foreach($in_aMaps as $mid => $sel)
		if($sel)
		{
			$map = sys::int($mid);

			// Проверка наличия карты
			$sql->query('SELECT `id`, `name` FROM `maps` WHERE `id`="'.$map.'" AND `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'" LIMIT 1');
			if(!$sql->num())
				continue;

			$map = $sql->get();

			$cp = 'cp /path/maps/'.$server['game'].'/'.sys::map($map['name']).'.* '.$dir.'maps/;'
				.'cd /path/maps/'.$server['game'].'/'.sys::map($map['name']).'/ && cp -r * '.$dir;

			$ssh->set('sudo -u server'.$server['uid'].' screen -dmS mc'.$start_point.$id.' sh -c \''.$cp.'\'');
		}

	sys::outjs(array('s' => 'ok'), $nmch);
?>
