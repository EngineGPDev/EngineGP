<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'Партнеры';
	$html->nav($title);

	$html->get('partners');
	$html->pack('main');
?>