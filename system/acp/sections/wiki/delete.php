<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['type']) AND $url['type'] == 'cat')
		$sql->query('DELETE FROM `wiki_category` WHERE `id`="'.$id.'" LIMIT 1');
	else{
		$sql->query('DELETE FROM `wiki` WHERE `id`="'.$id.'" LIMIT 1');
		$sql->query('DELETE FROM `wiki_answer` WHERE `wiki`="'.$id.'" LIMIT 1');
	}

	sys::outjs(array('s' => 'ok'));
?>