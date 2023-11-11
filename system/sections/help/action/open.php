<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($user['group'] == 'support' AND $user['level'] < 2)
		sys::outjs(array('e' => 'У вас нет доступа к данному действию.'));

	if($id)
	{
		if(in_array($user['group'], array('admin', 'support')))
			$sql->query('UPDATE `help` set `close`="0", `time`="'.$start_point.'" WHERE `id`="'.$id.'" LIMIT 1');
		else
			$sql->query('UPDATE `help` set `close`="0", `time`="'.$start_point.'" WHERE `id`="'.$id.'" AND `user`="'.$user['id'].'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	sys::outjs(array('e' => 'Вопрос не найден в базе.'));
?>