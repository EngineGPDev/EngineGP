<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
		$aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : '';

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		$name = md5(time().rand(5, 100).rand(10, 20).rand(1, 20).rand(40, 80));

		$file = fopen(FILES.'pages/'.$name, "w");

		fputs($file, $aData['text']);

		fclose($file);

		$sql->query('INSERT INTO `pages` set `name`="'.htmlspecialchars($aData['name']).'", `file`="'.$name.'"');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('add', 'sections/pages');

	$html->pack('main');
?>