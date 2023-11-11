<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
		$aData['address'] = isset($_POST['address']) ? trim($_POST['address']) : '';
		$aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : '';
		$aData['sql_login'] = isset($_POST['sql_login']) ? trim($_POST['sql_login']) : '';
		$aData['sql_passwd'] = isset($_POST['sql_passwd']) ? trim($_POST['sql_passwd']) : '';
		$aData['sql_port'] = isset($_POST['sql_port']) ? sys::int($_POST['sql_port']) : 3306;
		$aData['sql_ftp'] = isset($_POST['sql_ftp']) ? trim($_POST['sql_ftp']) : '';
		$aData['cs'] = isset($_POST['cs']) ? trim($_POST['cs']) : 0;
		$aData['cssold'] = isset($_POST['cssold']) ? $_POST['cssold'] : 0;
		$aData['css'] = isset($_POST['css']) ? $_POST['css'] : 0;
		$aData['csgo'] = isset($_POST['csgo']) ? $_POST['csgo'] : 0;
		$aData['samp'] = isset($_POST['samp']) ? $_POST['samp'] : 0;
		$aData['crmp'] = isset($_POST['crmp']) ? $_POST['crmp'] : 0;
		$aData['mta'] = isset($_POST['mta']) ? $_POST['mta'] : 0;
		$aData['mc'] = isset($_POST['mc']) ? $_POST['mc'] : 0;
		$aData['ram'] = isset($_POST['ram']) ? sys::int($_POST['ram']) : 0;
		$aData['test'] = isset($_POST['test']) ? sys::int($_POST['test']) : 0;
		$aData['show'] = isset($_POST['show']) ? $_POST['show'] : 0;
		$aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;
		$aData['domain'] = isset($_POST['domain']) ? trim($_POST['domain']) : '';

		foreach(array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc') as $game)
			$aData[$game] = (string) $aData[$game] == 'on' ? '1' : '0';

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		include(LIB.'ssh.php');

		if(!$ssh->auth($aData['passwd'], $aData['address']))
			sys::outjs(array('e' => 'Не удалось создать связь с локацией'));

		$sql->query('INSERT INTO `units` set '
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
			.'`domain`="'.$aData['domain'].'"');

		sys::outjs(array('s' => $sql->id()));
	}

	$html->get('add', 'sections/units');

	$html->pack('main');
?>