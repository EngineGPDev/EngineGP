<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `id` FROM `copy` WHERE `server`="'.$id.'" ORDER BY `id` DESC LIMIT 3');
	if($sql->num() > 2)
		sys::outjs(array('e' => 'Для создания новой копии необходимо удалить старые.'), $nmch);

	$sql->query('SELECT `id` FROM `copy` WHERE `server`="'.$id.'" AND `status`="0" LIMIT 1');
	if($sql->num())
		sys::outjs(array('e' => 'Для создания новой копии дождитесь создания предыдущей.'), $nmch);

	$aSel = array();

	$aData = isset($_POST['copy']) ? $_POST['copy'] : sys::outjs(array('e' => 'Для создания копии необходимо выбрать директории/файлы.'), $nmch);

	foreach(params::$section_copy[$server['game']]['aCopy'] as $name => $info)
	{
		if(!isset($aData['\''.$name.'\'']))
			continue;

		$aSel[] = $name;
	}

	if(!count($aSel))
		sys::outjs(array('e' => 'Для создания копии необходимо выбрать директории/файлы.'), $nmch);

	$copy = '';
	$info = '';
	$plugins = '';

	foreach($aSel as $name)
	{
		$copy .= isset(params::$section_copy[$server['game']]['aCopyDir'][$name]) ? params::$section_copy[$server['game']]['aCopyDir'][$name].' ' : '';
		$copy .= isset(params::$section_copy[$server['game']]['aCopyFile'][$name]) ? params::$section_copy[$server['game']]['aCopyFile'][$name].' ' : '';

		$info .= $name.', ';
	}

	$name_copy = md5($start_point.$id.$server['game']);

	$ssh->set('cd '.$tarif['install'].$server['uid'].' && screen -dmS copy_'.$server['uid'].' sh -c "tar -cf '.$name_copy.'.tar '.$copy.'; mv '.$name_copy.'.tar /copy"');

	$sql->query('SELECT `plugin`, `upd` FROM `plugins_install` WHERE `server`="'.$id.'"');
	while($plugin = $sql->get())
		$plugins .= $plugin['plugin'].'.'.$plugin['upd'].',';

	$sql->query('INSERT INTO `copy` set `user`="'.$server['user'].'_'.$server['unit'].'", `game`="'.$server['game'].'", `server`="'.$id.'", `pack`="'.$server['pack'].'", `name`="'.$name_copy.'", `info`="'.substr($info, 0, -2).'",  `plugins`="'.substr($plugins, 0, -1).'", `date`="'.$start_point.'", `status`="0"');

	// Очистка кеша
	$mcache->delete('server_copy_'.$id);

	sys::outjs(array('s' => 'ok'), $nmch);
?>