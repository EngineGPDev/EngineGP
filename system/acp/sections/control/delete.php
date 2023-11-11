<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('UPDATE `control` set `user`="-1", `status`="overdue", `time`="0", `overdue`="0" WHERE `id`="'.$id.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'));
?>