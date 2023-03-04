<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($id)
		include(SEC.'control/server.php');
	else{
		$list = '';

		$servers = $sql->query('SELECT `id`, `user`, `address`, `overdue`, `date`, `status`, `limit`, `price` FROM `control` WHERE `user`!="-1" AND `time`<"'.$start_point.'" AND `overdue`>"'.$start_point.'" ORDER BY `id` ASC');
		while($ctrl = $sql->get())
		{
			$list .= '<tr>';
				$list .= '<td class="text-center">'.$ctrl['id'].'</td>';
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/control/id/'.$ctrl['id'].'">'.$ctrl['address'].'</a></td>';
				$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $ctrl['date']).'</td>';
				$list .= '<td class="text-center">'.$ctrl['limit'].' шт.</td>';
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'control/id/'.$ctrl['id'].'" target="_blank">Перейти</a></td>';
			$list .= '</tr>';

			$list .= '<tr>';
				$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$ctrl['user'].'">USER_'.$ctrl['user'].'</a></td>';
				$list .= '<td class="text-center">'.$status[$ctrl['status']].'</td>';
				$list .= '<td class="text-center">Удаление через: '.sys::date('min', $ctrl['overdue']+$cfg['control_delete']*86400).'</td>';
				$list .= '<td class="text-center">'.$ctrl['price'].' '.$cfg['currency'].'</td>';
				$list .= '<td class="text-center"><a href="#" onclick="return control_delete(\''.$ctrl['id'].'\')" class="text-red">Удалить</a></td>';
			$list .= '</tr>';
		}

		$html->get('index', 'sections/control');
			$html->set('list', $list);
			$html->set('pages', '');
		$html->pack('main');
	}
?>