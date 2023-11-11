<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['get']) AND $url['get'] == 'list')
	{
		$unit = isset($url['unit']) ? sys::int($url['unit']) : sys::out();
		$game = isset($url['game']) ? $url['game'] : sys::out();

		if(!in_array($game, array('cs', 'cssold', 'css', 'csgo')))
			sys::out();

		$maps = '';

		$sql->query('SELECT `name` FROM `maps` WHERE `unit`="'.$unit.'" AND `game`="'.$game.'" ORDER BY `id` ASC');
		
		$all = 'Общее число карт: '.$sql->num().' шт.'.PHP_EOL;
		
		while($map = $sql->get())
			$maps .= $map['name'].PHP_EOL;

		$maps = $maps == '' ? 'В базе нет карт' : $all.$maps.$all;

		sys::out($maps);
	}

	if($go)
	{
		$unit = isset($url['unit']) ? sys::int($url['unit']) : sys::outjs(array('e' => 'Необходимо выбрать локацию'));
		$game = isset($url['game']) ? $url['game'] : sys::outjs(array('e' => 'Необходимо выбрать игру'));

		if(!$unit)
			sys::outjs(array('e' => 'Необходимо выбрать локацию'));

		if(!in_array($game, array('cs', 'cssold', 'css', 'csgo')))
			sys::outjs(array('e' => 'Необходимо выбрать игру'));

		include(LIB.'ssh.php');

		$sql->query('SELECT `id`, `passwd`, `address` FROM `units` WHERE `id`="'.$unit.'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Локация не найдена'));

		$unit = $sql->get();

		if(!$ssh->auth($unit['passwd'], $unit['address']))
			sys::outjs(array('e' => 'Не удалось создать связь с локацией'));

		$sql->query('DELETE FROM `maps` WHERE `unit`="'.$unit['id'].'" AND `game`="'.$game.'"');

		$maps = $ssh->get('cd /path/maps/'.$game.' && ls | grep .bsp | grep -v .bsp.');

		$aMaps = explode("\n", $maps);

		array_pop($aMaps);

		foreach($aMaps as $map)
		{
			$name = array_shift(explode('.', $map));

			$sql->query('INSERT INTO `maps` set `unit`="'.$unit['id'].'", `game`="'.$game.'", `name`="'.$name.'"');
		}

		sys::outjs(array('s' => 'ok'));
	}

	$units = '';

	$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
	while($unit = $sql->get())
		$units .= '<option value="'.$unit['id'].'">'.$unit['name'].'</option>';

	$html->get('updmp', 'sections/addons');

		$html->set('units', $units);

	$html->pack('main');
?>