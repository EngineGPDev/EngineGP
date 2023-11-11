<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-users"></i> Управление пользователями';

	$aSection = array(
		'index',
		'online',
		'offline',
		'signup',
		'stats',
		'delete'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/users');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

		$sql->query('SELECT `id` FROM `users`');
		$all = $sql->num();

		$sql->query('SELECT `id` FROM `users` WHERE `time`>"'.($start_point-180).'"');
		$online = $sql->num();

		$html->set('all', $all);
		$html->set('online', $online);
		$html->set('offline', $all-$online);

		$sql->query('SELECT `id` FROM `signup`');
		$html->set('signup', $sql->num());

	$html->pack('menu');

	include(SEC.'users/'.$section.'.php');
?>