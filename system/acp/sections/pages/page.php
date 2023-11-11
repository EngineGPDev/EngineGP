<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `name`, `file` FROM `pages` WHERE `id`="'.$id.'" LIMIT 1');
	$page = $sql->get();

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $page['name'];
		$aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : file_get_contents(FILES.'pages/'.$page['file']);

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		$file = fopen(FILES.'pages/'.$page['file'], "w");

		fputs($file, $aData['text']);

		fclose($file);

		$sql->query('UPDATE `pages` set `name`="'.htmlspecialchars($aData['name']).'" WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('page', 'sections/pages');
		
		$html->set('id', $id);
		$html->set('name', htmlspecialchars_decode($page['name']));

		$html->set('text', htmlspecialchars(file_get_contents(FILES.'pages/'.$page['file'])));

	$html->pack('main');
?>