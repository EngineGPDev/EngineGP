<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `file` FROM `pages` WHERE `id`="'.$id.'" LIMIT 1');
	$page = $sql->get();

	unlink(FILES.'pages/'.$page['file']);

	$sql->query('DELETE FROM `pages` WHERE `id`="'.$id.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'));
?>