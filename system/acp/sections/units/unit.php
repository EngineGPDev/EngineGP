<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT * FROM `units` WHERE `id`="'.$id.'" LIMIT 1');
	$unit = $sql->get();

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $unit['name'];
		$aData['address'] = isset($_POST['address']) ? trim($_POST['address']) : $unit['address'];
		$aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : $unit['passwd'];
		$aData['sql_login'] = isset($_POST['sql_login']) ? trim($_POST['sql_login']) : $unit['sql_login'];
		$aData['sql_passwd'] = isset($_POST['sql_passwd']) ? trim($_POST['sql_passwd']) : $unit['sql_passwd'];
		$aData['sql_port'] = isset($_POST['sql_port']) ? sys::int($_POST['sql_port']) : $unit['sql_port'];
		$aData['sql_ftp'] = isset($_POST['sql_ftp']) ? trim($_POST['sql_ftp']) : $unit['sql_ftp'];
		$aData['cs'] = isset($_POST['cs']) ? $_POST['cs'] : $unit['cs'];
		$aData['cssold'] = isset($_POST['cssold']) ? $_POST['cssold'] : $unit['cssold'];
		$aData['css'] = isset($_POST['css']) ? $_POST['css'] : $unit['css'];
		$aData['csgo'] = isset($_POST['csgo']) ? $_POST['csgo'] : $unit['csgo'];
		$aData['samp'] = isset($_POST['samp']) ? $_POST['samp'] : $unit['samp'];
		$aData['crmp'] = isset($_POST['crmp']) ? $_POST['crmp'] : $unit['crmp'];
		$aData['mta'] = isset($_POST['mta']) ? $_POST['mta'] : $unit['mta'];
		$aData['mc'] = isset($_POST['mc']) ? $_POST['mc'] : $unit['mc'];
		$aData['ram'] = isset($_POST['ram']) ? sys::int($_POST['ram']) : $unit['ram'];
		$aData['test'] = isset($_POST['test']) ? sys::int($_POST['test']) : $unit['test'];
		$aData['show'] = isset($_POST['show']) ? $_POST['show'] : $unit['show'];
		$aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : $unit['sort'];
		$aData['domain'] = isset($_POST['domain']) ? trim($_POST['domain']) : $unit['domain'];

		foreach(array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc') as $game)
			$aData[$game] = (string) $aData[$game] == 'on' ? '1' : '0';

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		include(LIB.'ssh.php');

		if(!$ssh->auth($aData['passwd'], $aData['address']))
			sys::outjs(array('e' => 'Не удалось создать связь с локацией'));

		$sql->query('UPDATE `units` set '
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`address`="'.$aData['address'].'",'
			.'`passwd`="'.$aData['passwd'].'",'
			.'`sql_login`="'.$aData['sql_login'].'",'
			.'`sql_passwd`="'.$aData['sql_passwd'].'",'
			.'`sql_port`="'.$aData['sql_port'].'",'
			.'`sql_ftp`="'.$aData['sql_ftp'].'",'
			.'`cs`="'.$aData['cs'].'",'
			.'`cssold`="'.$aData['cssold'].'",'
			.'`css`="'.$aData['css'].'",'
			.'`csgo`="'.$aData['csgo'].'",'
			.'`samp`="'.$aData['samp'].'",'
			.'`crmp`="'.$aData['crmp'].'",'
			.'`mta`="'.$aData['mta'].'",'
			.'`mc`="'.$aData['mc'].'",'
			.'`ram`="'.$aData['ram'].'",'
			.'`test`="'.$aData['test'].'",'
			.'`show`="'.$aData['show'].'",'
			.'`sort`="'.$aData['sort'].'",'
			.'`domain`="'.$aData['domain'].'" WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => $id));
	}

	$html->get('unit', 'sections/units');

		foreach($unit as $i => $val)
			$html->set($i, $val);

		foreach(array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc') as $game)
		{
			if($unit[$game])
				$html->unit('game_'.$game, 1);
			else
				$html->unit('game_'.$game);
		}

		$html->set('show', $unit['show'] == 1 ? '<option value="1">Доступна</option><option value="0">Недоступна</option>' : '<option value="0">Недоступна</option><option value="1">Доступна</option>');

	$html->pack('main');
?>