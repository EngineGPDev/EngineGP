<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$cid = isset($url['cid']) ? sys::int($url['cid']) : sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$sql->query('SELECT `id`, `pack`, `name`, `info`, `plugins`, `date`, `status` FROM `copy` WHERE `id`="'.$cid.'" AND `user`="'.$server['user'].'_'.$server['unit'].'" AND `game`="'.$server['game'].'" LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$copy = $sql->get();

	if(!$copy['status'])
		sys::outjs(array('e' => 'Дождитесь создания резервной копии.'), $nmch);

	if($copy['pack'] != $server['pack'])
	{
		$sql->query('SELECT `packs` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
		$tarif = array_merge($tarif, $sql->get());

		$aPack = sys::b64djs($tarif['packs'], true);

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

	$ssh->set('cd '.$tarif['install'].$server['uid'].' && screen -dmS rec_'.$server['uid'].' sh -c "'
		.$rm.'; cp /copy/'.$copy['name'].'.tar . && tar -xf '.$copy['name'].'.tar; rm '.$copy['name'].'.tar;'
		.'find . -type d -exec chmod 700 {} \;;'
		.'find . -type f -exec chmod 600 {} \;;'
		.'chmod 500 '.params::$aFileGame[$server['game']].';'
		.'chown -R servers'.$server['uid'].':servers ."');

	// Удаление плагинов
	$sql->query('DELETE FROM `plugins_install` WHERE `server`="'.$id.'"');

	// Установка плагинов (имитирование)
	$aPlugins = explode(',', $copy['plugins']);

	foreach($aPlugins as $plugin)
	{
		$aPlugin = explode('.', $plugin);

		if(!count($aPlugin != 2))
			continue;

		if(!$aPlugin[0])
			continue;

		$sql->query('SELECT `id` FROM `plugins_install` WHERE `plugin`="'.$aPlugin[0].'" AND `server`="'.$id.'" LIMIT 1');

		if(!$aPlugin[1])
			$aPlugin[1] = 0;

		if(!$sql->num())
			$sql->query('INSERT INTO `plugins_install` set `server`="'.$id.'", `plugin`="'.$aPlugin[0].'", `upd`="'.$aPlugin[1].'", `time`="'.$copy['date'].'"');
	}

	// Очистка кеша
	$mcache->delete('server_plugins_'.$id);

	$sql->query('UPDATE `servers` set `status`="recovery" WHERE `id`="'.$id.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>