<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'API интерфейс';
	$html->nav($title);

	$html->get('api');
	$html->pack('main');
?>