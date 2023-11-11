<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT * FROM `tarifs` WHERE `id`="'.$id.'" LIMIT 1');
	$tarif = $sql->get();

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $tarif['name'];
		$aData['unit'] = isset($_POST['unit']) ? sys::int($_POST['unit']) : $tarif['unit'];
		$aData['game'] = isset($_POST['game']) ? trim($_POST['game']) : $tarif['game'];
		$aData['slots'] = isset($_POST['slots']) ? trim($_POST['slots']) : $tarif['slots'];
		$aData['posts'] = isset($_POST['posts']) ? trim($_POST['posts']) : $tarif['posts'];
		$aData['hostname'] = isset($_POST['hostname']) ? trim($_POST['hostname']) : $tarif['hostname'];
		$aData['packs'] = isset($_POST['packs']) ? trim($_POST['packs']) : $tarif['packs'];
		$aData['path'] = isset($_POST['path']) ? trim($_POST['path']) : $tarif['path'];
		$aData['install'] = isset($_POST['install']) ? trim($_POST['install']) : $tarif['install'];
		$aData['update'] = isset($_POST['update']) ? trim($_POST['update']) : $tarif['update'];
		$aData['fps'] = isset($_POST['fps']) ? trim($_POST['fps']) : $tarif['fps'];
		$aData['tickrate'] = isset($_POST['tickrate']) ? trim($_POST['tickrate']) : $tarif['tickrate'];
		$aData['ram'] = isset($_POST['ram']) ? trim($_POST['ram']) : $tarif['ram'];
		$aData['param_fix'] = isset($_POST['param_fix']) ? trim($_POST['param_fix']) : $tarif['param_fix'];
		$aData['time'] = isset($_POST['time']) ? trim($_POST['time']) : $tarif['time'];
		$aData['timext'] = isset($_POST['timext']) ? trim($_POST['timext']) : $tarif['timext'];
		$aData['test'] = isset($_POST['test']) ? sys::int($_POST['test']) : $tarif['test'];
		$aData['tests'] = isset($_POST['tests']) ? sys::int($_POST['tests']) : $tarif['tests'];
		$aData['discount'] = isset($_POST['discount']) ? sys::int($_POST['discount']) : $tarif['discount'];
		$aData['map'] = isset($_POST['map']) ? trim($_POST['map']) : $tarif['map'];
		$aData['ftp'] = isset($_POST['ftp']) ? $_POST['ftp'] : $tarif['ftp'];
		$aData['plugins'] = isset($_POST['plugins']) ? $_POST['plugins'] : $tarif['plugins'];
		$aData['console'] = isset($_POST['console']) ? $_POST['console'] : $tarif['console'];
		$aData['stats'] = isset($_POST['stats']) ? $_POST['stats'] : $tarif['stats'];
		$aData['copy'] = isset($_POST['copy']) ? $_POST['copy'] : $tarif['copy'];
		$aData['web'] = isset($_POST['web']) ? $_POST['web'] : $tarif['web'];
		$aData['plugins_install'] = isset($_POST['plugins_install']) ? trim($_POST['plugins_install']) : $tarif['plugins_install'];
		$aData['hdd'] = isset($_POST['hdd']) ? sys::int($_POST['hdd']) : $tarif['hdd'];
		$aData['autostop'] = isset($_POST['autostop']) ? sys::int($_POST['autostop']) : $tarif['autostop'];
		$aData['price'] = isset($_POST['price']) ? trim($_POST['price']) : $tarif['price'];
		$aData['core_fix'] = isset($_POST['core_fix']) ? trim($_POST['core_fix']) : $tarif['core_fix'];
		$aData['ip'] = isset($_POST['ip']) ? trim($_POST['ip']) : $tarif['ip'];
		$aData['show'] = isset($_POST['show']) ? sys::int($_POST['show']) : $tarif['show'];
		$aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : $tarif['sort'];

		if($aData['name'] == '')
			sys::outjs(array('e' => 'Необходимо указать название'));

		$sql->query('SELECT `id` FROM `units` WHERE `id`="'.$aData['unit'].'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Необходимо указать локацию'));

		if(!in_array($aData['game'], array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc')))
			sys::outjs(array('e' => 'Необходимо указать игру'));

		$aSlots = explode('-', $aData['slots']);

		if(!isset($aSlots[0]) || !isset($aSlots[1]))
			sys::outjs(array('e' => 'Необходимо указать слоты'));

		$aSlots[0] = trim($aSlots[0]);
		$aSlots[1] = trim($aSlots[1]);

		$aData['slots_min'] = $aSlots[0] > 1 ? $aSlots[0] : sys::outjs(array('e' => 'Неправильно указаны слоты'));
		$aData['slots_max'] = $aSlots[1] >= $aSlots[0] ? $aSlots[1] : sys::outjs(array('e' => 'Неправильно указаны слоты'));

		$aPorts = explode('-', $aData['posts']);

		if(!isset($aPorts[0]) || !isset($aPorts[1]))
			sys::outjs(array('e' => 'Необходимо указать порты'));

		$aPorts[0] = trim($aPorts[0]);
		$aPorts[1] = trim($aPorts[1]);

		$aData['port_min'] = $aPorts[0] > 1 ? $aPorts[0] : sys::outjs(array('e' => 'Неправильно указаны порты'));
		$aData['port_max'] = $aPorts[1] >= $aPorts[0] ? $aPorts[1] : sys::outjs(array('e' => 'Неправильно указаны порты'));

		if($aData['hostname'] == '')
			sys::outjs(array('e' => 'Необходимо указать название сервера'));

		if($aData['path'] == '')
			sys::outjs(array('e' => 'Необходимо указать путь до сборок'));

		if($aData['install'] == '')
			sys::outjs(array('e' => 'Необходимо указать путь для установки серверов'));

		if($aData['update'] == '' AND !in_array($aData['game'], array('css', 'csgo')))
			sys::outjs(array('e' => 'Необходимо указать путь до обновления сборки'));

		if(substr($aData['path'], -1) != '/' || substr($aData['install'], -1) != '/' || (substr($aData['update'], -1) != '/' AND !in_array($aData['game'], array('css', 'csgo'))))
			sys::outjs(array('e' => 'Пути должны заканчиваться символом "/"'));

		$int = array(
			'Тестов' => 'tests',
			'Положение' => 'sort',
			'Диск' => 'hdd'
		);

		foreach($int as $name => $input)
		{
			if($aData[$input] == '')
				sys::outjs(array('e' => 'Необходимо указать поле "'.$name.'"'));
		}

		$aPacks = explode(',', $aData['packs']);

		$packs = array();

		foreach($aPacks as $pack)
		{
			$aPack = explode(':', trim($pack));

			if(!isset($aPack[0]) || !isset($aPack[1]))
				continue;

			$name = str_replace('"', '', $aPack[0]);
			$fullname = str_replace('"', '', $aPack[1]);

			$packs[trim($name)] = trim($fullname);
		}

		if(!count($packs))
			sys::outjs(array('e' => 'Необходимо указать минимум одну сборку'));

		$aData['packs'] = sys::b64js($packs);

		$aIp = explode(':', $aData['ip']);

		$ips = '';

		foreach($aIp as $ip)
		{
			$ip = trim($ip);

			if(sys::valid($ip, 'ip'))
				continue;

			$ips .= $ip.':';
		}

		$ips = isset($ips{0}) ? substr($ips, 0, -1) : '';

		$aData['ip'] = $ips;

		$aPlugins = explode('","', $aData['plugins_install']);

		$plugins = array();

		foreach($aPlugins as $plugin)
		{
			$aPlugin = explode(':', trim($plugin));

			if(!isset($aPlugin[0]) || !isset($aPlugin[1]))
				continue;

			$name = trim(str_replace('"', '', $aPlugin[0]));

			if(!isset($packs[$name]))
				continue;

			$aList = explode(',', str_replace('"', '', $aPlugin[1]));

			$list = '';

			foreach($aList as $pid)
			{
				$pid = trim($pid);

				if(!is_numeric($pid))
					continue;

				$list .= intval($pid).',';
			}

			$list = isset($list{0}) ? substr($list, 0, -1) : '';

			if($list == '')
				continue;

			$plugins[$name] = $list;
		}

		$aData['plugins_install'] = count($plugins) ? sys::b64js($plugins) : ''; 

		$aCores = explode(',', $aData['core_fix']);

		$cores = '';

		foreach($aCores as $core)
		{
			$core = trim($core);

			if(!is_numeric($core))
				continue;

			if($core < 1)
				continue;

			$cores .= intval($core).',';
		}

		$cores = isset($cores{0}) ? substr($cores, 0, -1) : '';

		$aData['core_fix'] = $cores;

		$aTime = explode(':', $aData['time']);

		$times = '';

		foreach($aTime as $time)
		{
			$time = trim($time);

			if(!is_numeric($time))
				continue;

			$times .= intval($time).':';
		}

		$times = isset($times{0}) ? substr($times, 0, -1) : '';

		$aData['time'] = $times;

		$aTimext = explode(':', $aData['timext']);

		$timexts = '';

		foreach($aTimext as $timext)
		{
			$timext = trim($timext);

			if(!is_numeric($timext))
				continue;

			$timexts .= intval($timext).':';
		}

		$timexts = isset($timexts{0}) ? substr($timexts, 0, -1) : '';

		$aData['timext'] = $timexts;

		$aFps = explode(':', $aData['fps']);

		$sfps = '';

		foreach($aFps as $fps)
		{
			$fps = trim($fps);

			if(!is_numeric($fps))
				continue;

			$sfps .= intval($fps).':';
		}

		$sfps = isset($sfps{0}) ? substr($sfps, 0, -1) : '';

		$aData['fps'] = $sfps;

		$aTick = explode(':', $aData['tickrate']);

		$stick = '';

		foreach($aTick as $tick)
		{
			$tick = trim($tick);

			if(!is_numeric($tick))
				continue;

			$stick .= intval($tick).':';
		}

		$stick = isset($stick{0}) ? substr($stick, 0, -1) : '';

		$aData['tickrate'] = $stick;

		$aRam = explode(':', $aData['ram']);

		$sram = '';

		foreach($aRam as $ram)
		{
			$ram = trim($ram);

			if(!is_numeric($ram))
				continue;

			$sram .= intval($ram).':';
		}

		$sram = isset($sram{0}) ? substr($sram, 0, -1) : '';

		$aData['ram'] = $sram;

		$aPrice = explode(':', $aData['price']);

		$sprice = '';

		foreach($aPrice as $price)
		{
			$price = trim($price);

			if(!is_numeric($price))
				continue;

			$sprice .= $price.':';
		}

		$sprice = isset($sprice{0}) ? substr($sprice, 0, -1) : '';

		$aData['price'] = $sprice;

		switch($aData['game'])
		{
			case 'cs':
				if(count(explode(':', $aData['fps'])) != count(explode(':', $aData['price'])))
					sys::outjs(array('e' => 'Неправильно указано поле "Цена"'));

				break;

			case 'cssold':
				$afps = explode(':', $aData['fps']);
				$atick = explode(':', $aData['tickrate']);
				$aprice = explode(':', $aData['price']);

				if((count($afps)*count($atick)) != count($aprice))
					sys::outjs(array('e' => 'Неправильно указано поле "Цена"'));

				$price = array();

				$i = 0;

				foreach($afps as $fps)
				{
					foreach($atick as $tick)
					{
						$price[$tick.'_'.$fps] = $aprice[$i];

						$i+=1;
					}
				}

				$aData['price'] = sys::b64js($price);

				break;

			case 'css':
			case 'csgo':
				if(count(explode(':', $aData['tickrate'])) != count(explode(':', $aData['price'])))
					sys::outjs(array('e' => 'Неправильно указано поле "Цена"'));

				break;

			case 'mc':
				if(count(explode(':', $aData['ram'])) != count(explode(':', $aData['price'])))
					sys::outjs(array('e' => 'Неправильно указано поле "Цена"'));

		}

		$access = array('ftp', 'plugins', 'console', 'stats', 'copy', 'web');

		foreach($access as $section)
			$aData[$section] = (string) $aData[$section] == 'on' ? '1' : '0';

		$sql->query('UPDATE `tarifs` set'
			.'`unit`="'.$aData['unit'].'",'
			.'`game`="'.$aData['game'].'",'
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`slots_min`="'.$aData['slots_min'].'",'
			.'`slots_max`="'.$aData['slots_max'].'",'
			.'`port_min`="'.$aData['port_min'].'",'
			.'`port_max`="'.$aData['port_max'].'",'
			.'`hostname`="'.htmlspecialchars($aData['hostname']).'",'
			.'`packs`="'.$aData['packs'].'",'
			.'`path`="'.addslashes($aData['path']).'",'
			.'`install`="'.addslashes($aData['install']).'",'
			.'`update`="'.addslashes($aData['update']).'",'
			.'`fps`="'.$aData['fps'].'",'
			.'`tickrate`="'.$aData['tickrate'].'",'
			.'`ram`="'.$aData['ram'].'",'
			.'`param_fix`="'.$aData['param_fix'].'",'
			.'`time`="'.$aData['time'].'",'
			.'`timext`="'.$aData['timext'].'",'
			.'`test`="'.$aData['test'].'",'
			.'`tests`="'.$aData['tests'].'",'
			.'`discount`="'.$aData['discount'].'",'
			.'`map`="'.addslashes($aData['map']).'",'
			.'`ftp`="'.$aData['ftp'].'",'
			.'`plugins`="'.$aData['plugins'].'",'
			.'`console`="'.$aData['console'].'",'
			.'`stats`="'.$aData['stats'].'",'
			.'`copy`="'.$aData['copy'].'",'
			.'`web`="'.$aData['web'].'",'
			.'`plugins_install`="'.$aData['plugins_install'].'",'
			.'`hdd`="'.$aData['hdd'].'",'
			.'`autostop`="'.$aData['autostop'].'",'
			.'`price`="'.$aData['price'].'",'
			.'`core_fix`="'.$aData['core_fix'].'",'
			.'`ip`="'.$aData['ip'].'",'
			.'`show`="'.$aData['show'].'",'
			.'`sort`="'.$aData['sort'].'" WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

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

	$units = str_replace('"'.$tarif['unit'].'"', '"'.$tarif['unit'].'" selected="select"', $units);
	$games = str_replace('"'.$tarif['game'].'"', '"'.$tarif['game'].'" selected="select"', $games);

	$html->get('tarif', 'sections/tarifs');

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