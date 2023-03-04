<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `key` FROM `api` WHERE `server`="'.$id.'" LIMIT 1');
	if($sql->num())
		$sql->query('DELETE FROM `api` WHERE `server`="'.$id.'" LIMIT 1');
	else
		$sql->query('INSERT INTO `api` set `server`="'.$id.'", `key`="'.md5(sys::passwd(10)).'"');

	$mcache->delete('server_settings_'.$id);

	sys::back($cfg['http'].'servers/id/'.$id.'/section/settings');
?>