<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$list = '';

	$aGroup = array('user' => 'Пользователь', 'support' => 'Тех. поддержка', 'admin' => 'Администратор');

	$sql->query('SELECT `id` FROM `users` WHERE `time`>"'.($start_point-180).'"');

	$aPage = sys::page($page, $sql->num(), 20);

	sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/users/section');

	include(LIB.'geo.php');

	$SxGeo = new SxGeo(DATA.'SxGeoCity.dat');

	$sql->query('SELECT `id`, `login`, `group`, `ip`, `browser`  FROM `users` WHERE `time`>"'.($start_point-180).'" ORDER BY `id` ASC LIMIT '.$aPage['num'].', 20');
	while($us = $sql->get())
	{
		$list .= '<tr>';
			$list .= '<td>'.$us['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/users/id/'.$us['id'].'">'.$us['login'].'</a></td>';
			$list .= '<td>'.$aGroup[$us['group']].'</td>';
			$list .= '<td>'.sys::browser($us['browser']).'</td>';
			$list .= '<td>'.$us['ip'].'</td>';
			$list .= '<td>'.sys::country($us['ip']).'</td>';
			$list .= '<td><a href="#" onclick="return users_delete(\''.$us['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$html->get('online', 'sections/users');

		$html->set('list', $list);
		$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

	$html->pack('main');
?>