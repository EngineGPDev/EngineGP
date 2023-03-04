<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$cid = isset($url['cid']) ? sys::int($url['cid']) : sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$sql->query('SELECT `id`, `pack`, `name`, `info`, `plugins`, `date`, `status` FROM `control_copy` WHERE `id`="'.$cid.'" AND `user`="'.$ctrl['user'].'_'.$id.'" AND `game`="'.$server['game'].'" LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$copy = $sql->get();

	if(!$copy['status'])
		sys::outjs(array('e' => 'Дождитесь создания резервной копии.'), $nmch);

	if($copy['pack'] != $server['pack'])
	{
		$aPack = $cfg['control_packs'][$server['game']];

		sys::outjs(array('e' => 'Для восстановления необходимо установить сборку: '.$aPack[$copy['pack']].'.'), $nmch);
	}

	if(params::$section_copy[$server['game']]['CopyFull'] == $copy['info'])
		$rm = 'rm -r '.$copy['info'];
	else{
		$rm = '';

		$aInfo = explode(', ', $copy['info']);

		foreach($aInfo as $name)
		{
			$rm .= isset(params::$section_copy[$server['game']]['aCopyDir'][$name]) ? 'rm -r '.params::$section_copy[$server['game']]['aCopyDir'][$name].' ' : '';
			$rm .= isset(params::$section_copy[$server['game']]['aCopyFile'][$name]) ? 'rm '.params::$section_copy[$server['game']]['aCopyFile'][$name].' ' : '';
		}

	}

	$ssh->set('cd /servers/'.$server['uid'].' && screen -dmS rec_'.$server['uid'].' sh -c "'
		.$rm.'; cp /copy/'.$copy['name'].'.tar . && tar -xf '.$copy['name'].'.tar; rm '.$copy['name'].'.tar;'
		.'find . -type d -exec chmod 700 {} \;;'
		.'find . -type f -exec chmod 600 {} \;;'
		.'chmod 500 '.params::$aFileGame[$server['game']].';'
		.'chown -R servers'.$server['uid'].':servers ."');

	// Удаление плагинов
	$sql->query('DELETE FROM `control_plugins_install` WHERE `server`="'.$sid.'"');

	// Установка плагинов (имитирование)
	$aPlugins = explode(',', $copy['plugins']);

	foreach($aPlugins as $plugin)
	{
		$aPlugin = explode('.', $plugin);

		if(!count($aPlugin != 2))
			continue;

		if(!$aPlugin[0])
			continue;

		$sql->query('SELECT `id` FROM `control_plugins_install` WHERE `plugin`="'.$aPlugin[0].'" AND `server`="'.$sid.'" LIMIT 1');

		if(!$aPlugin[1])
			$aPlugin[1] = 0;

		if(!$sql->num())
			$sql->query('INSERT INTO `control_plugins_install` set `server`="'.$sid.'", `plugin`="'.$aPlugin[0].'", `upd`="'.$aPlugin[1].'", `time`="'.$copy['date'].'"');
	}

	// Очистка кеша
	$mcache->delete('ctrl_server_plugins_'.$sid);

	$sql->query('UPDATE `control_servers` set `status`="recovery" WHERE `id`="'.$sid.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>