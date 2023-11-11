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
		$wikis = $sql->query('SELECT `id`, `name`, `cat`, `date` FROM `wiki` WHERE `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`name` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`cat` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`tags` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$wiki = $sql->query('SELECT `id`, `name`, `cat`, `date` FROM `wiki` WHERE '.$like.' ORDER BY `id` ASC LIMIT 20');
	}

	if(!$sql->num($wikis))
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$list = '';

	while($wiki = $sql->get($wikis))
	{
		$sql->query('SELECT `name` FROM `wiki_category` WHERE `id`="'.$wiki['cat'].'" LIMIT 1');
		$cat = $sql->get();

		$list .= '<tr>';
			$list .= '<td>'.$wiki['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/wiki/id/'.$wiki['id'].'">'.$wiki['name'].'</a></td>';
			$list .= '<td>'.$cat['name'].'</td>';
			$list .= '<td>'.date('d.m.Y - H:i:s', $wiki['date']).'</td>';
			$list .= '<td class="text-center"><a href="#" onclick="return wiki_delete(\''.$wiki['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>