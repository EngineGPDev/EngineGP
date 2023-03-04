<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$text = isset($_POST['text']) ? trim($_POST['text']) : '';

	$mkey = md5($text.$id);

	$cache = $mcache->get($mkey);

	if(is_array($cache))
	{
		if($go)
			sys::outjs($cache, $nmch);

		sys::outjs($cache);
	}

	if(!isset($text{2}))
	{
		if($go)
			sys::outjs(array('e' => 'Для выполнения поиска, необходимо больше данных'), $nmch);

		sys::outjs(array('e' => ''));
	}

	$select = '`id`, `unit`, `tarif`, `user`, `address`, `game`, `status`, `slots`, `name`, `time` FROM `servers` WHERE `user`!="-1" AND';

	if(isset($url['search']) AND in_array($url['search'], array('unit', 'tarif')))
		$select .= ' `'.$url['search'].'`='.sys::int($url[$url['search']]).' AND';

	$check = explode('=', $text);

	if(in_array($check[0], array('game', 'unit', 'core', 'tarif', 'user', 'status', 'slots')))
	{
		$val = trim($check[1]);

		switch($check[0])
		{
			case 'game':
				if(in_array($val, array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc')))
					$servers = $sql->query('SELECT '.$select.' FROM `servers` WHERE `user`!="-1" AND `game`="'.$val.'" ORDER BY `id` ASC');
			break;

			case 'unit':
				$servers = $sql->query('SELECT '.$select.' `unit`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'core':
				$servers = $sql->query('SELECT '.$select.' `core_use`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'tarif':
				$servers = $sql->query('SELECT '.$select.' `tarif`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'user':
				$servers = $sql->query('SELECT '.$select.' `user`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'status':
				if(in_array($val, array('working', 'start', 'change', 'restart', 'off', 'overdue', 'blocked', 'recovery', 'reinstall', 'update', 'install')))
					$servers = $sql->query('SELECT '.$select.' `status`="'.$val.'" ORDER BY `id` ASC');
			break;

			case 'slots':
				$servers = $sql->query('SELECT '.$select.' `slots`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;
		}
	}elseif($text{0} == 'i' AND $text{1} == 'd')
		$servers = $sql->query('SELECT '.$select.' `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`name` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`game` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`slots` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`status` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`address` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`port` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$servers = $sql->query('SELECT '.$select.' ('.$like.') ORDER BY `id` ASC');
	}

	if(!$sql->num($servers))
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$status = array(
		'working' => '<span class="text-green">Работает</span>',
		'off' => '<span class="text-red">Выключен</span>',
		'start' => 'Запускается',
		'restart' => 'Перезапускается',
		'change' => 'Смена карты',
		'install' => 'Устанавливается',
		'reinstall' => 'Переустанавливается',
		'update' => 'Обновляется',
		'recovery' => 'Восстанавливается',
		'overdue' => 'Просрочен',
		'blocked' => 'Заблокирован'
	);

	$list = '';

	while($server = $sql->get($servers))
	{
		$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
		$unit = $sql->get();

		$sql->query('SELECT `name` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
		$tarif = $sql->get();

		$list .= '<tr>';
			$list .= '<td class="text-center">'.$server['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/servers/id/'.$server['id'].'">'.$server['name'].'</a></td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/servers/search/unit/unit/'.$server['unit'].'">#'.$server['unit'].' '.$unit['name'].'</a></td>';
			$list .= '<td class="text-center">'.$server['slots'].' шт.</td>';
			$list .= '<td class="text-center">'.strtoupper($server['game']).'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'servers/id/'.$server['id'].'" target="_blank">Перейти</a></td>';
		$list .= '</tr>';

		$list .= '<tr>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$server['user'].'">USER_'.$server['user'].'</a></td>';
			$list .= '<td>'.$server['address'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/servers/search/tarif/tarif/'.$server['tarif'].'">#'.$server['tarif'].' '.$tarif['name'].'</a></td>';
			$list .= '<td class="text-center">'.$status[$server['status']].'</td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $server['time']).'</td>';
			$list .= '<td class="text-center"><a href="#" onclick="return servers_delete(\''.$server['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>