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

	if(substr($text, 0, 5) == 'game=')
	{
		$game = trim(substr($text, 5));

		if(in_array($game, array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc')))
			$webs = $sql->query('SELECT `id`, `type`, `server`, `user`, `unit`, `domain`, `passwd`, `login`, `date` FROM `web` WHERE `game`="'.$game.'" ORDER BY `id` ASC');
	}elseif($text{0} == 'i' AND $text{1} == 'd')
		$webs = $sql->query('SELECT `id`, `type`, `server`, `user`, `unit`, `domain`, `passwd`, `login`, `date` FROM `web` WHERE `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`type` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`server` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`user` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`unit` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`domain` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`login` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$webs = $sql->query('SELECT `id`, `type`, `server`, `user`, `unit`, `domain`, `passwd`, `login`, `date` FROM `web` WHERE '.$like.' ORDER BY `id` ASC');
	}

	if(!$sql->num($webs))
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$list = '';

	while($web = $sql->get($webs))
	{
		if(!$web['unit'])
			$unit = array('name' => 'Веб хостинг');
		else{
			$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$web['unit'].'" LIMIT 1');
			$unit = $sql->get();
		}

		$list .= '<tr>';
			$list .= '<td class="text-center">'.$web['id'].'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$web['user'].'">USER_'.$web['user'].'</a></td>';
			$list .= '<td class="text-center"><a href="http://'.$web['domain'].'" target="_blank">'.$web['domain'].'</a></td>';
			$list .= '<td class="text-center">'.$web['login'].'</td>';
			$list .= '<td class="text-center">'.date('H:i:s', $web['date']).'</td>';
			$list .= '<td class="text-center"><a target="_blank" href="'.$cfg['http'].'servers/id/'.$web['server'].'/section/web/subsection/'.$web['type'].'/action/manage">Перейти</a></td>';
		$list .= '</tr>';

		$list .= '<tr>';
			$list .= '<td class="text-center">'.$aWebname[$web['type']].'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/servers/id/'.$web['server'].'">SERVER_'.$web['server'].'</a></td>';
			$list .= '<td class="text-center">'.$unit['name'].'</td>';
			$list .= '<td class="text-center">'.$web['passwd'].'</td>';
			$list .= '<td class="text-center">'.date('d.n.Y', $web['date']).'</td>';
			$list .= '<td class="text-center"><a href="#" onclick="return web_delete(\''.$web['server'].'\', \''.$web['type'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>