<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$cid = isset($url['cid']) ? sys::int($url['cid']) : sys::outjs(array('e' => 'Выбранная копия не найдена.'), $nmch);

	$sql->query('SELECT `id`, `pack`, `name`, `plugins`, `date`, `status` FROM `copy` WHERE `id`="'.$cid.'" AND `user`="'.$server['user'].'_'.$server['unit'].'" AND `game`="'.$server['game'].'" LIMIT 1');
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

	$ssh->set('cd '.$tarif['install'].$server['uid'].' && screen -dmS rec_'.$server['uid'].' sh -c "'
		.'cp /copy/'.$copy['name'].'.tar . && tar -xf '.$copy['name'].'.tar; rm '.$copy['name'].'.tar;'
		.'find . -type d -exec chmod 700 {} \;;'
		.'find . -type f -exec chmod 600 {} \;;'
		.'chmod 500 '.params::$aFileGame[$server['game']].';'
		.'chown -R servers'.$server['uid'].':servers ."');

	// Установка плагинов (имитирование)
	$aPlugin = explode(',', $copy['plugins']);

	foreach($aPlugin as $plugin)
	{
		if(!$plugin)
			continue;

		$sql->query('SELECT `id` FROM `plugins_install` WHERE `plugin`="'.$plugin.'" AND `server`="'.$id.'" LIMIT 1');

		if(!$sql->num())
			$sql->query('INSERT INTO `plugins_install` set `server`="'.$id.'", `plugin`="'.$plugin.'", `time`="'.$copy['date'].'"');
	}

	// Очистка кеша
	$mcache->delete('server_plugins_'.$id);

	$sql->query('UPDATE `servers` set `status`="recovery" WHERE `id`="'.$id.'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>