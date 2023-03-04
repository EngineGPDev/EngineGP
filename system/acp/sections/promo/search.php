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
		$promos = $sql->query('SELECT `id`, `cod`, `value`, `discount`, `use`, `extend`, `tarif`, `time` FROM `promo` WHERE `id`="'.sys::int($text).'" LIMIT 1');
	else{
		$like = '`id` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`cod` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\') OR'
				.'`value` LIKE FROM_BASE64(\''.base64_encode('%'.str_replace('_', '\_', $text).'%').'\')';

		$promos = $sql->query('SELECT `id`, `cod`, `value`, `discount`, `use`, `extend`, `tarif`, `time` FROM `promo` WHERE '.$like.' ORDER BY `id` ASC LIMIT 20');
	}

	if(!$sql->num($promos))
	{
		if($go)
			sys::outjs(array('e' => 'По вашему запросу ничего не найдено'), $nmch);

		sys::outjs(array('e' => 'По вашему запросу ничего не найдено'));
	}

	$list = '';

	while($promo = $sql->get($promos))
	{
		$sql->query('SELECT `name` FROM `tarifs` WHERE `id`="'.$promo['tarif'].'" LIMIT 1');
		$tarif = $sql->get();

		$list .= '<tr>';
			$list .= '<td>'.$promo['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/promo/id/'.$promo['id'].'">'.$promo['cod'].'</a></td>';
			$list .= '<td class="text-center">'.$promo['value'].'</td>';
			$list .= '<td class="text-center">#'.$promo['tarif'].' '.$tarif['name'].'</td>';
			$list .= '<td class="text-center">'.($promo['discount'] ? 'Скидка' : 'Подарочные дни').'</td>';
			$list .= '<td class="text-center">'.($promo['extend'] ? 'Продление' : 'Аренда').'</td>';
			$list .= '<td class="text-center">'.$promo['use'].' шт.</td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $promo['time']).'</td>';
			$list .= '<td class="text-center"><a href="#" onclick="return promo_delete(\''.$tarif['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$mcache->set($mkey, array('s' => $list), false, 15);

	sys::outjs(array('s' => $list));
?>