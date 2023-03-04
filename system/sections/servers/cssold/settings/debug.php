<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    $html->nav('Отладочный лог');

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::back($cfg['http'].'servers/id/'.$id.'/section/settings');

	// Чтение файла - oldstart.log
	$file = $tarif['install'].$server['uid'].'/debug.log';

	$ssh->set('echo "" >> '.$file.' && cat '.$file.' | grep "CRASH: " | grep -ve "^#\|^[[:space:]]*$"');

	$html->get('debug', 'sections/servers/games/settings');

		$html->set('log', htmlspecialchars($ssh->get()));

	$html->pack('main');
?>