<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('DELETE FROM `promo` WHERE `id`="'.$id.'" LIMIT 1');
	$sql->query('DELETE FROM `promo_use` WHERE `promo`="'.$id.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'));
?>