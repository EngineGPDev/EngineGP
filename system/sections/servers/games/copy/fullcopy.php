<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `id` FROM `copy` WHERE `server`="'.$id.'" AND `info`="'.params::$section_copy[$server['game']]['CopyFull'].'" LIMIT 1');
	if($sql->num())
		sys::outjs(array('e' => 'Для создания новой копии необходимо удалить старую.'), $nmch);

	$name_copy = md5($start_point.$id.$server['game']);

	$ssh->set('cd '.$tarif['install'].$server['uid'].' && screen -dmS copy_'.$server['uid'].' sh -c "tar -cf '.$name_copy.'.tar '.params::$section_copy[$server['game']]['CopyFull'].'; mv '.$name_copy.'.tar /copy"');

	$plugins = '';

	$sql->query('SELECT `plugin`, `upd` FROM `plugins_install` WHERE `server`="'.$id.'"');
	while($plugin = $sql->get())
		$plugins .= $plugin['plugin'].'.'.$plugin['upd'].',';

	$sql->query('INSERT INTO `copy` set `user`="'.$server['user'].'_'.$server['unit'].'", `game`="'.$server['game'].'", `server`="'.$id.'", `pack`="'.$server['pack'].'", `name`="'.$name_copy.'", `info`="'.params::$section_copy[$server['game']]['CopyFull'].'",  `plugins`="'.substr($plugins, 0, -1).'", `date`="'.$start_point.'", `status`="0"');

	// Очистка кеша
	$mcache->delete('server_copy_'.$id);

	sys::outjs(array('s' => 'ok'), $nmch);
?>