<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go || !isset($url['server']))
		exit;
	
	include(LIB.'web/free.php');

	$aData = array();

	$aData['server'] = sys::int($url['server']);
	$aData['type'] = $url['subsection'];
	$aData['user'] = $server['user'];
	$aData['file'] = 'cstrike/addons/amxmodx/configs/sql.cfg';
	$aData['cfg'] = 'cstrike/server.cfg';

	$aData['orcfg'] = array();
	$aData['orsql'] = array();

	web::connect($aData, $nmch);
?>