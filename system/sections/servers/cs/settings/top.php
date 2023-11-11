<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::back($cfg['http'].'servers/id/'.$id.'/section/settings');

	// Удаление файла csstats.dat
	$ssh->set('rm '.$tarif['install'].$server['uid'].'/cstrike/addons/amxmodx/data/csstats.dat');

	if(in_array($server['status'], array('working', 'start', 'restart', 'change')))
	{
		shell_exec('php cron.php '.$cfg['cron_key'].' server_action restart cs '.$id);

		sys::outjs(array('s' => 'ok'));
	}
?>