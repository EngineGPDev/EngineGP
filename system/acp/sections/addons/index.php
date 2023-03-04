<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['subsection']) AND $url['subsection'] == 'search')
		include(SEC.'addons/search.php');

	if($id)
		include(SEC.'addons/plugin.php');
	else{
		$sort_page = '';
		$sort_sql = 'ORDER BY `id` ASC';

		if(isset($url['sort']) AND in_array($url['sort'], array('id', 'cat', 'game')))
		{
			$sort = 'asc';

			if(isset($url['sorting']))
				$sort = $url['sorting'] == 'asc' ? 'asc' : 'desc';

			$sort_page = '/sort/'.$url['sort'].'/sorting/'.$sort;
			$sort_sql = 'ORDER BY `'.$url['sort'].'` '.$sort;

			$sort_icon = array($url['sort'] => $sort);
		}

		$list = '';

		$sql->query('SELECT `id` FROM `plugins`');

		$aPage = sys::page($page, $sql->num(), 20);

		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/addons'.$sort_page);

		$status = array(0 => 'Стабильный', 1 => 'Нестабильный', 2 => 'Тестируемый');

		$plugins = $sql->query('SELECT `id`, `cat`, `game`, `name`, `status` FROM `plugins` '.$sort_sql.' LIMIT '.$aPage['num'].', 20');
		while($plugin = $sql->get($plugins))
		{
			$sql->query('SELECT `name` FROM `plugins_category` WHERE `id`="'.$plugin['cat'].'" LIMIT 1');
			$cat = $sql->get();

			$list .= '<tr>';
				$list .= '<td>'.$plugin['id'].'</td>';
				$list .= '<td><a href="'.$cfg['http'].'acp/addons/id/'.$plugin['id'].'">'.$plugin['name'].'</a></td>';
				$list .= '<td>'.$cat['name'].'</td>';
				$list .= '<td>'.$status[$plugin['status']].'</td>';
				$list .= '<td>'.strtoupper($plugin['game']).'</td>';
				$list .= '<td><a href="#" onclick="return plugins_delete(\''.$plugin['id'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/addons');

			$html->set('sort_id', 'asc');
			$html->set('sort_cat', 'asc');
			$html->set('sort_game', 'asc');

			if(isset($sort_icon))
				$html->set('sort_'.key($sort_icon), array_shift($sort_icon));

			$html->set('list', $list);

			$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

		$html->pack('main');
	}
?>