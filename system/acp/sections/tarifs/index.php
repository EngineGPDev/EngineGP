<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['subsection']) AND $url['subsection'] == 'search')
		include(SEC.'tarifs/search.php');

	if($id)
		include(SEC.'tarifs/tarif.php');
	else{
		$sort_page = '';
		$sort_sql = 'ORDER BY `id` ASC';

		if(isset($url['sort']) AND in_array($url['sort'], array('id', 'unit', 'game')))
		{
			$sort = 'asc';

			if(isset($url['sorting']))
				$sort = $url['sorting'] == 'asc' ? 'asc' : 'desc';

			$sort_page = '/sort/'.$url['sort'].'/sorting/'.$sort;
			$sort_sql = 'ORDER BY `'.$url['sort'].'` '.$sort;

			$sort_icon = array($url['sort'] => $sort);
		}

		$list = '';

		$sql->query('SELECT `id` FROM `tarifs`');

		$aPage = sys::page($page, $sql->num(), 20);

		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/tarif'.$sort_page);

		$tarifs = $sql->query('SELECT `id`, `unit`, `game`, `name`, `slots_min`, `slots_max`, `port_min`, `port_max` FROM `tarifs` '.$sort_sql.' LIMIT '.$aPage['num'].', 20');
		while($tarif = $sql->get($tarifs))
		{
			$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$tarif['unit'].'" LIMIT 1');
			$unit = $sql->get();

			$list .= '<tr>';
				$list .= '<td>'.$tarif['id'].'</td>';
				$list .= '<td><a href="'.$cfg['http'].'acp/tarifs/id/'.$tarif['id'].'">'.$tarif['name'].'</a></td>';
				$list .= '<td>#'.$tarif['unit'].' '.$unit['name'].'</td>';
				$list .= '<td>'.$tarif['slots_min'].'-'.$tarif['slots_max'].'</td>';
				$list .= '<td>'.$tarif['port_min'].'-'.$tarif['port_max'].'</td>';
				$list .= '<td>'.strtoupper($tarif['game']).'</td>';
				$list .= '<td><a href="'.$cfg['http'].'acp/tarifs/section/copy/id/'.$tarif['id'].'">Копировать</a></td>';
				$list .= '<td><a href="#" onclick="return tarifs_delete(\''.$tarif['id'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/tarifs');

			$html->set('sort_id', 'asc');
			$html->set('sort_unit', 'asc');
			$html->set('sort_game', 'asc');

			if(isset($sort_icon))
				$html->set('sort_'.key($sort_icon), array_shift($sort_icon));

			$html->set('list', $list);

			$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

		$html->pack('main');
	}
?>