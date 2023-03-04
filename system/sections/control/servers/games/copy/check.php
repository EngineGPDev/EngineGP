<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$nmch = 'ctrl_server_copy_check_'.$sid;

	if($mcache->get($nmch))
		sys::outjs(array('e' => sys::text('other', 'mcache')));

	$mcache->set($nmch, true, false, 10);

	$copys = $sql->query('SELECT `id` FROM `control_copy` WHERE `user`="'.$ctrl['user'].'_'.$id.'" AND `status`="0"');
	if(!$sql->num($copys))
		sys::outjs(array('e' => 'no find'), $nmch);

	while($copy = $sql->get($copys))
	{
		if(!sys::int($ssh->get('ps aux | grep copy_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
			$sql->query('UPDATE `control_copy` set `status`="1" WHERE `id`="'.$copy['id'].'" LIMIT 1');
	}

	// Очистка кеша
	$mcache->delete('ctrl_server_copy_'.$sid);

	sys::outjs(array('s' => 'ok'), $nmch);
?>