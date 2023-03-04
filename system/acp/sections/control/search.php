<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$text = isset($_POST['text']) ? trim($_POST['text']) : '';

	$mkey = md5($text.'control');

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

	$select = '`id`, `user`, `address`, `time`, `date`, `status`, `limit`, `price` FROM `control` WHERE `user`!="-1" AND';

	$check = explode('=', $text);

	if(in_array($check[0], array('limit', 'price', 'user', 'status')))
	{
		$val = trim($check[1]);

		switch($check[0])
		{
			case 'limit':
				$ctrls = $sql->query('SELECT '.$select.' `limit`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'price':
				$ctrls = $sql->query('SELECT '.$select.' `price`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'ctrls':
				$ctrl = $sql->query('SELECT '.$select.' `user`="'.sys::int($val).'" ORDER BY `id` ASC');
			break;

			case 'status':
				if(in_array($val, array('working', 'error', 'reboot', 'overdue', 'blocked', 'install')))
					$ctrls = $sql->query('SELECT '.$select.' `status`="'.$val.'" ORDER BY `id` ASC');
		}
	}elseif($text{0} == 'i' AND $text{1} == 'd')
		$ctrls = $sql->query('SELECT '.$select.' `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`address` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$ctrls = $sql->query('SELECT '.$select.' ('.$like.') ORDER BY `id` ASC');
	}

	if(!$sql->num($ctrls))
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$status = array(
		'working' => '<span class="text-green">Работает</span>',
		'reboot' => 'перезагружается',
		'error' => '<span class="text-red">Не отвечает</span>',
		'install' => 'Настраивается',
		'overdue' => 'Просрочен',
		'blocked' => 'Заблокирован'
	);

	$list = '';

	while($ctrl = $sql->get($ctrls))
	{
		$list .= '<tr>';
			$list .= '<td class="text-center">'.$ctrl['id'].'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/control/id/'.$ctrl['id'].'">'.$ctrl['address'].'</a></td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $ctrl['date']).'</td>';
			$list .= '<td class="text-center">'.$ctrl['limit'].' шт.</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'control/id/'.$ctrl['id'].'" target="_blank">Перейти</a></td>';
		$list .= '</tr>';

		$list .= '<tr>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$ctrl['user'].'">USER_'.$ctrl['user'].'</a></td>';
			$list .= '<td class="text-center">'.$status[$ctrl['status']].'</td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $ctrl['time']).'</td>';
			$list .= '<td class="text-center">'.$ctrl['price'].' '.$cfg['currency'].'</td>';
			$list .= '<td class="text-center"><a href="#" onclick="return control_delete(\''.$ctrl['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>