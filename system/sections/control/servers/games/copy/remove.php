<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$cid = isset($url['cid']) ? sys::int($url['cid']) : sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$sql->query('SELECT `name`, `status` FROM `control_copy` WHERE `id`="'.$cid.'" AND `user`="'.$ctrl['user'].'_'.$id.'" AND `game`="'.$server['game'].'" LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$copy = $sql->get();

	if(!$copy['status'])
		sys::outjs(array('e' => 'Дождитесь создания резервной копии.'), $nmch);

	$ssh->set('screen -dmS rem_copy_'.$cid.' rm /copy/'.$copy['name'].'.tar');

	$sql->query('DELETE FROM `control_copy` WHERE `id`="'.$cid.'" LIMIT 1');

	// Очистка кеша
	$mcache->delete('ctrl_server_copy_'.$sid);

	sys::outjs(array('s' => 'ok'), $nmch);
?>