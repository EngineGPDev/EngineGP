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
		$notices = $sql->query('SELECT `id`, `unit`, `server`, `text`, `time` FROM `notice` WHERE `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`unit` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`server` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`text` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$notices = $sql->query('SELECT `id`, `unit`, `server`, `text`, `time` FROM `notice` WHERE '.$like.' ORDER BY `id` ASC LIMIT 20');
	}

	if(!$sql->num($notices))
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$list = '';

	while($notice = $sql->get($notices))
	{
		if($notice['unit'])
		{
			$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$notice['unit'].'" LIMIT 1');
			$unit = $sql->get();

			$name = $unit['name'];
		}else
			$name = '<a href="'.$cfg['http'].'acp/servers/id/'.$notice['server'].'">SERVER_'.$notice['server'].'</a>';

		$list .= '<tr>';
			$list .= '<td>'.$notice['id'].'</td>';
			$list .= '<td class="w50p">Адресовано: '.$name.'</td>';
			$list .= '<td>Завершится: '.date('d.m.Y - H:i:s', $notice['time']).'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/notice/id/'.$notice['id'].'">Редактировать</a></td>';
			$list .= '<td class="text-center"><a href="#" onclick="return notice_delete(\''.$notice['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';

		$list .= '<tr>';
			$list .= '<td colspan="5">'.$notice['text'].'</td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>