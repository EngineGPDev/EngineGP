<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	include(LIB.'web/free.php');

	$aData = array(
		'type' => $url['subsection'],
		'server' => array_merge($server, array('id' => $id))
	);

	web::update($aData, $nmch);
?>