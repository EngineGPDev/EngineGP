<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$list = '';

	$sql->query('SELECT `id` FROM `logs` WHERE `type`="cashout"');

	$aPage = sys::page($page, $sql->num(), 40);

	sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/logs/section/cashout');

	$sql->query('SELECT `id`, `user`, `text`, `date`, `money` FROM `logs` WHERE `type`="cashout" ORDER BY `id` DESC LIMIT '.$aPage['num'].', 40');
	while($log = $sql->get())
	{
		$list .= '<tr>';
			$list .= '<td>'.$log['id'].'</td>';
			$list .= '<td>'.$log['text'].'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$log['user'].'">USER_'.$log['user'].'</a></td>';
			$list .= '<td class="text-center">'.$log['money'].'</td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $log['date']).'</td>';
		$list .= '</tr>';
	}

	$html->get('logs', 'sections/logs');

		$html->set('list', $list);

		$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

	$html->pack('main');
?>