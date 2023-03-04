<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT * FROM `tarifs` WHERE `id`="'.$id.'" LIMIT 1');
	$tarif = $sql->get();

	$games = '<option value="cs">Counter-Strike: 1.6</option><option value="cssold">Counter-Strike: Source v34</option><option value="css">Counter-Strike: Source</option>'
		.'<option value="csgo">Counter-Strike: Global Offensive</option><option value="samp">San Andreas Multiplayer</option><option value="crmp">GTA: Criminal Russia</option>'
		.'<option value="mta">Multi Theft Auto</option><option value="mc">Minecraft</option>';

	$fix = $tarif['param_fix'] ? '<option value="1">Фиксированные параметры</option><option value="0">Не фиксированные параметры</option>' : '<option value="0">Не фиксированные параметры</option><option value="1">Фиксированные параметры</option>';
	$test = $tarif['test'] ? '<option value="1">Доступно</option><option value="0">Недоступно</option>' : '<option value="0">Недоступно</option><option value="1">Доступно</option>';
	$discount = $tarif['discount'] ? '<option value="1">Включены</option><option value="0">Без скидок</option>' : '<option value="0">Без скидок</option><option value="1">Включены</option>';
	$autostop = $tarif['autostop'] ? '<option value="1">Включено</option><option value="0">Выключено</option>' : '<option value="0">Выключено</option><option value="1">Включено</option>';
	$show = $tarif['show'] ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>';

	$units = '<option value="0">Выберете локацию</option>';

	$sql->query('SELECT `id`, `name` FROM `units` ORDER BY `id` ASC');
	while($unit = $sql->get())
		$units .= '<option value="'.$unit['id'].'">#'.$unit['id'].' '.$unit['name'].'</option>';

	$games = str_replace('"'.$tarif['game'].'"', '"'.$tarif['game'].'" selected="select"', $games);

	$html->get('copy', 'sections/tarifs');

		if($tarif['game'] == 'cssold')
		{
			$sprice = '';

			$aPrice = sys::b64djs($tarif['price']);

			foreach($aPrice as $price)
				$sprice .= $price.':';

			$sprice = isset($sprice{0}) ? substr($sprice, 0, -1) : '';

			$tarif['price'] = $sprice;
		}

		foreach($tarif as $field => $val)
			$html->set($field, $val);

		$html->set('units', $units);
		$html->set('games', $games);
		$html->set('param_fix', $fix);
		$html->set('test', $test);
		$html->set('discount', $discount);
		$html->set('autostop', $autostop);
		$html->set('show', $show);

		foreach(array('ftp', 'plugins', 'console', 'stats', 'copy', 'web') as $section)
		{
			if($tarif[$section])
				$html->unit($section, 1);
			else
				$html->unit($section);
		}

		$packs = '';

		$aPacks = sys::b64djs($tarif['packs']);

		foreach($aPacks as $name => $fullname)
			$packs .= '"'.$name.'":"'.$fullname.'",';

		$packs = isset($packs{0}) ? substr($packs, 0, -1) : '';

		$html->set('packs', $packs);

		$plugins = '';

		$aPlugins = sys::b64djs($tarif['plugins_install']);

		foreach($aPlugins as $pack => $list)
			$plugins .= '"'.$pack.'":"'.$list.'",';

		$plugins = isset($plugins{0}) ? substr($plugins, 0, -1) : '';

		$html->set('plugins_install', $plugins);

	$html->pack('main');
?>