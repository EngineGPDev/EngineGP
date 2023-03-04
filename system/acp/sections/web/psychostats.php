<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(DATA.'web.php');

	if(isset($url['subsection']) AND $url['subsection'] == 'search')
		include(SEC.'web/search.php');

	if($id)
		include(SEC.'web/web.php');
	else{
		$list = '';

		$sql->query('SELECT `id` FROM `web`');

		$aPage = sys::page($page, $sql->num(), 20);

		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/web');

		$webs = $sql->query('SELECT `id`, `type`, `server`, `user`, `unit`, `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="psychostats" ORDER BY `id` ASC LIMIT '.$aPage['num'].', 20');
		while($web = $sql->get($webs))
		{
			if(!$web['unit'])
				$unit = array('name' => 'Веб хостинг');
			else{
				$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$web['unit'].'" LIMIT 1');
				$unit = $sql->get();
			}

			$list .= '<tr>';
				$list .= '<td class="text-center">'.$web['id'].'</td>';
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$web['user'].'">USER_'.$web['user'].'</a></td>';
				$list .= '<td class="text-center"><a href="http://'.$web['domain'].'" target="_blank">'.$web['domain'].'</a></td>';
				$list .= '<td class="text-center">'.$web['login'].'</td>';
				$list .= '<td class="text-center">'.date('H:i:s', $web['date']).'</td>';
				$list .= '<td class="text-center"><a target="_blank" href="'.$cfg['http'].'servers/id/'.$web['server'].'/section/web/subsection/'.$web['type'].'/action/manage">Перейти</a></td>';
			$list .= '</tr>';

			$list .= '<tr>';
				$list .= '<td class="text-center">'.$aWebname[$web['type']].'</td>';
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/servers/id/'.$web['server'].'">SERVER_'.$web['server'].'</a></td>';
				$list .= '<td class="text-center">'.$unit['name'].'</td>';
				$list .= '<td class="text-center">'.$web['passwd'].'</td>';
				$list .= '<td class="text-center">'.date('d.n.Y', $web['date']).'</td>';
				$list .= '<td class="text-center"><a href="#" onclick="return web_delete(\''.$web['server'].'\', \''.$web['type'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/web');

			$html->set('list', $list);

			$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

		$html->pack('main');
	}
?>