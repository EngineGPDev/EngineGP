<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['delete']))
	{
		$sql->query('DELETE FROM `signup` WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	$list = '';

	$sql->query('SELECT `id`, `mail`, `key`, `date` FROM `signup` ORDER BY `id` ASC');
	while($sign = $sql->get())
	{
		$list .= '<tr>';
			$list .= '<td>'.$sign['id'].'</td>';
			$list .= '<td>'.$sign['mail'].'</td>';
			$list .= '<td>'.$sign['key'].'</td>';
			$list .= '<td>'.sys::today($sign['date']).'</td>';
			$list .= '<td><a href="#" onclick="return users_delete_signup(\''.$sign['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$html->get('signup', 'sections/users');

		$html->set('list', $list);

	$html->pack('main');
?>