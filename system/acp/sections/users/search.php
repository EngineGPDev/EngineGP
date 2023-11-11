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

	if($text{0} == 'i' AND $text{1} == 'd')
		$sql->query('SELECT `id`, `login`, `mail`, `balance`, `group` FROM `users` WHERE `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`login` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`mail` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`balance` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`group` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`phone` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`contacts` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`ip` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$sql->query('SELECT `id`, `login`, `mail`, `balance`, `group` FROM `users` WHERE '.$like.' ORDER BY `id` ASC LIMIT 10');
	}

	if(!$sql->num())
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$list = '';

	while($us = $sql->get())
	{
		$list .= '<tr>';
			$list .= '<td>'.$us['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/users/id/'.$us['id'].'">'.$us['login'].'</a></td>';
			$list .= '<td>'.$us['mail'].'</td>';
			$list .= '<td>'.$us['balance'].' '.$cfg['currency'].'</td>';
			$list .= '<td>'.$aGroup[$us['group']].'</td>';
			$list .= '<td><a href="#" onclick="return users_delete(\''.$us['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>