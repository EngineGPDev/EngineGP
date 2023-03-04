<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['subsection']) AND $url['subsection'] == 'search')
		include(SEC.'users/search.php');

	if($id)
		include(SEC.'users/user.php');
	else{
		$sort_page = '';
		$sort_sql = 'ORDER BY `id` ASC';

		if(isset($url['sort']) AND in_array($url['sort'], array('id', 'balance', 'group')))
		{
			$sort = 'asc';

			if(isset($url['sorting']))
				$sort = $url['sorting'] == 'asc' ? 'asc' : 'desc';

			$sort_page = '/sort/'.$url['sort'].'/sorting/'.$sort;
			$sort_sql = 'ORDER BY `'.$url['sort'].'` '.$sort;

			$sort_icon = array($url['sort'] => $sort);
		}

		$list = '';

		$aGroup = array('user' => 'Пользователь', 'support' => 'Тех. поддержка', 'admin' => 'Администратор');

		$sql->query('SELECT `id` FROM `users`');

		$aPage = sys::page($page, $sql->num(), 20);

		sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/users'.$sort_page);

		$sql->query('SELECT `id`, `login`, `mail`, `balance`, `group` FROM `users` '.$sort_sql.' LIMIT '.$aPage['num'].', 20');
		while($us = $sql->get())
		{
			$list .= '<tr>';
				$list .= '<td>'.$us['id'].'</td>';
				$list .= '<td><a href="'.$cfg['http'].'acp/users/id/'.$us['id'].'">'.$us['login'].'</a></td>';
				$list .= '<td>'.$us['mail'].'</td>';
				$list .= '<td>'.$us['balance'].' '.$cfg['currency'].'</td>';
				$list .= '<td>'.$aGroup[$us['group']].'</td>';
				$list .= '<td><a href="#" onclick="return users_delete(\''.$us['id'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/users');

			$html->set('sort_id', 'asc');
			$html->set('sort_balance', 'asc');
			$html->set('sort_group', 'asc');

			if(isset($sort_icon))
				$html->set('sort_'.key($sort_icon), array_shift($sort_icon));

			$html->set('list', $list);

			$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

		$html->pack('main');
	}
?>