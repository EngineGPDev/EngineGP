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

	$select = '`id`, `user`, `server`, `text`, `time` FROM `logs_sys`';

	$check = explode('=', $text);

	if(in_array($check[0], array('server', 'user')))
	{
		$val = trim($check[1]);

		switch($check[0])
		{
			case 'server':
				$sql->query('SELECT '.$select.' WHERE `server`="'.sys::int($val).'" ORDER BY `id` DESC');
			break;

			case 'user':
				$sql->query('SELECT '.$select.' WHERE `user`="'.sys::int($val).'" ORDER BY `id` DESC');
		}
	}elseif($text{0} == 'i' AND $text{1} == 'd')
		$sql->query('SELECT '.$select.' WHERE `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`user` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`server` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`text` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$sql->query('SELECT '.$select.' WHERE '.$like.' ORDER BY `id` DESC LIMIT 40');
	}

	if(!$sql->num())
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$list = '';

	while($log = $sql->get())
	{
		$list .= '<tr>';
			$list .= '<td>'.$log['id'].'</td>';
			$list .= '<td>'.$log['text'].'</td>';

			if(!$log['user'])
				$list .= '<td class="text-center">Система</td>';
			else
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$log['user'].'">USER_'.$log['user'].'</a></td>';

			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/server/id/'.$log['server'].'">SERVER_'.$log['server'].'</a></td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $log['time']).'</td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>