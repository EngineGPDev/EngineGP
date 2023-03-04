<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    $html->nav('Отладочный лог');

	$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$unit = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/settings');

	// Чтение файла - oldstart.log
	$file = '/servers/'.$server['uid'].'/debug.log';

	$ssh->set('echo "" >> '.$file.' && cat '.$file.' | grep "CRASH: " | grep -ve "^#\|^[[:space:]]*$"');

	$html->get('debug', 'sections/control/servers/games/settings');

		$html->set('log', htmlspecialchars($ssh->get(), NULL, ''));

	$html->pack('main');
?>