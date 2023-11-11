<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aGames = array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc');

		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
		$aData['cs'] = isset($_POST['cs']) ? trim($_POST['cs']) : 0;
		$aData['cssold'] = isset($_POST['cssold']) ? $_POST['cssold'] : 0;
		$aData['css'] = isset($_POST['css']) ? $_POST['css'] : 0;
		$aData['csgo'] = isset($_POST['csgo']) ? $_POST['csgo'] : 0;
		$aData['samp'] = isset($_POST['samp']) ? $_POST['samp'] : 0;
		$aData['crmp'] = isset($_POST['crmp']) ? $_POST['crmp'] : 0;
		$aData['mta'] = isset($_POST['mta']) ? $_POST['mta'] : 0;
		$aData['mc'] = isset($_POST['mc']) ? $_POST['mc'] : 0;
		$aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

		foreach($aGames as $game)
			$aData[$game] = (string) $aData[$game] == 'on' ? '1' : '0';

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		foreach($aGames as $game)
		{
			if(!$aData[$game])
				continue;

			$sql->query('INSERT INTO `plugins_category` set '
				.'`game`="'.$game.'",'
				.'`name`="'.htmlspecialchars($aData['name']).'",'
				.'`sort`="'.$aData['sort'].'"');
		}

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('addcat', 'sections/addons');

	$html->pack('main');
?>