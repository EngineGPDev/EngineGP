<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `id` FROM `servers` WHERE `tarif`="'.$id.'" LIMIT 1');
	if($sql->num())
		sys::outjs(array('e' => 'Нельзя удалить тариф с серверами.'));

	$sql->query('DELETE FROM `tarifs` WHERE `id`="'.$id.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'));
?>