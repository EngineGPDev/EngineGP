<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Установка карт');

	// Категории для быстрой сортировки
	$html->get('types', 'sections/servers/'.$server['game'].'/maps');
		$html->set('id', $id);
	$html->pack('types');

	$type = false;

	include(DATA.'maps.php');
	include(LIB.'games/games.php');

	if(isset($url['type']) AND array_key_exists($url['type'], $aFindMap[$server['game']]))
		$type = $url['type'];

	if($type)
	{
		$qsql = games::mapsql($aFindMap[$server['game']][$type]);

		$all = $mcache->get('maps_'.$server['game'].'_'.$type);

		if(!$all)
		{
			$sql->query('SELECT `id` FROM `maps` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'" '.$qsql);
			$all = $sql->num();

			$mcache->set('maps_'.$server['game'].'_'.$type, $all, false, 120);
		}

		// Массив для построения страниц
		$aPage = sys::page($page, $all, 60);

		// Генерация массива ($html->arr['pages']) страниц
		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'servers/id/'.$id.'/section/maps/subsection/list/type/'.$type);

		$sql->query('SELECT `id`, `name` FROM `maps` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'" '.$qsql.' ORDER BY `name` ASC LIMIT '.$aPage['num'].', 30');
	}else{
		$all = $mcache->get('maps_'.$server['game']);
		
		if(!$all)
		{
			$sql->query('SELECT `id` FROM `maps` WHERE `unit`="'.$server['unit'].'" AND `game`="'.$server['game'].'"');
			$all = $sql->num();

			$mcache->set('maps_'.$server['game'], $all, false, 120);
		}

		// Массив для построения страниц
		$aPage = sys::page($page, $all, 30);

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