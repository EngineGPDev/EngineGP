<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$unit = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/settings');

	// Удаление файла csstats.dat
	$ssh->set('rm /servers'.$server['uid'].'/cstrike/addons/amxmodx/data/csstats.dat');

	if(in_array($server['status'], array('working', 'start', 'restart', 'change')))
		shell_exec('php cron.php '.$cfg['cron_key'].' control_server_action restart cs '.$sid);

	sys::outjs(array('s' => 'ok'));
?>