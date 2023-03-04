<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `id` FROM `control_copy` WHERE `server`="'.$sid.'" AND `info`="'.params::$section_copy[$server['game']]['CopyFull'].'" LIMIT 1');
	if($sql->num())
		sys::outjs(array('e' => 'Для создания новой копии необходимо удалить старую.'), $nmch);

	$name_copy = md5($id.$start_point.$sid.$server['game']);

	$ssh->set('cd /servers/'.$server['uid'].' && screen -dmS copy_'.$server['uid'].' sh -c "tar -cf '.$name_copy.'.tar '.params::$section_copy[$server['game']]['CopyFull'].'; mv '.$name_copy.'.tar /copy"');

	$plugins = '';

	$sql->query('SELECT `plugin`, `upd` FROM `control_plugins_install` WHERE `server`="'.$sid.'"');
	while($plugin = $sql->get())
		$plugins .= $plugin['plugin'].'.'.$plugin['upd'].',';

	$sql->query('INSERT INTO `control_copy` set `user`="'.$ctrl['user'].'_'.$id.'", `game`="'.$server['game'].'", `server`="'.$sid.'", `pack`="'.$server['pack'].'", `name`="'.$name_copy.'", `info`="'.params::$section_copy[$server['game']]['CopyFull'].'",  `plugins`="'.substr($plugins, 0, -1).'", `date`="'.$start_point.'", `status`="0"');

	// Очистка кеша
	$mcache->delete('ctrl_server_copy_'.$sid);

	sys::outjs(array('s' => 'ok'), $nmch);
?>