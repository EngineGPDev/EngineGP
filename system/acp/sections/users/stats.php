<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$list = '';

	$uss = $sql->query('SELECT `id`, `login`, `mail`, `balance` FROM `users` WHERE `group`="user" AND `balance`!="0" ORDER BY `balance` DESC LIMIT 10');
	while($us = $sql->get($uss))
	{
		$sql->query('SELECT `id` FROM `servers` WHERE `user`="'.$us['id'].'" LIMIT 10');
		$servers = $sql->num();

		$list .= '<tr>';
			$list .= '<td>'.$us['id'].'</td>';
			$list .= '<td><a href="'.$cfg['url'].'/acp/users/id/'.$us['id'].'">'.$us['login'].'</a></td>';
			$list .= '<td>'.$us['mail'].'</td>';
			$list .= '<td>'.$us['balance'].' '.$cfg['currency'].'</td>';
			$list .= '<td class="text-right">'.$servers.' шт.</td>';
		$list .= '</tr>';
	}

	$html->get('stats', 'sections/users');

		$html->set('list', $list);

		$sql->query('SELECT `balance` FROM `users` WHERE `group`="user" AND `balance`!="0"');
		$html->set('users', $sql->num());

		$money = 0;
		while($us = $sql->get())
			$money += $us['balance'];

		$html->set('money', $money);

	$html->pack('main');
?>