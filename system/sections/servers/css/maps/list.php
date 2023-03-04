<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Установка карт');

	// Категории для быстрой сортировки
	$html->get('types', 'sections/servers/'.$server['game'].'/maps');

		$html->set('id', $id);

	$html->pack('types');

	$type = false;

	if(isset($url['type']) AND in_array($url['type'], array('de', 'cs', 'aim', 'awp', 'bhop', 'csde', 'deathrun', 'jail')))
		$type = '^'.$url['type'].'\_';

	if($type)
		$sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'" AND `name` REGEXP FROM_BASE64(\''.base64_encode($type).'\') ORDER BY `name` ASC LIMIT 72');
	else{
		$sql->query('SELECT `id` FROM `maps` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'"');

		// Массив для построения страниц
		$aPage = sys::page($page, $sql->num(), 30);

		// Генерация массива ($html->arr['pages']) страниц
		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'servers/id/'.$id.'/section/maps/subsection/list');

		$sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'" ORDER BY `name` ASC LIMIT '.$aPage['num'].', 30');
	}

	$mapsjs = '';
	$i = 0;

	while($map = $sql->get())
	{
		$i+=1;

		$mapsjs .= $i.' : "'.$map['id'].'",';

		$html->get('map_install', 'sections/servers/games/maps');

			$html->set('id', $map['id']);
			$html->set('img', sys::img($map['name'], $server['game']));
			$html->set('name', $map['name']);
			
		$html->pack('maps');
	}

	$html->get('install', 'sections/servers/games/maps');

		$html->set('id', $id);

		$html->set('types', isset($html->arr['types']) ? $html->arr['types'] : '');
		$html->set('maps', isset($html->arr['maps']) ? $html->arr['maps'] : 'К сожалению карты не найдены в базе');
		$html->set('amaps', $mapsjs);
		$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');
		$html->set('cdn', $cfg['cdn']);

	$html->pack('main');
?>