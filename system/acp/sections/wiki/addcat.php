<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
		$aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		$sql->query('INSERT INTO `wiki_category` set '
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`sort`="'.$aData['sort'].'"');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('addcat', 'sections/wiki');

	$html->pack('main');
?>