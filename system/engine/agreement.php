<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'Договор оферты';
	$html->nav($title);

	$html->get('agreement');
	$html->pack('main');
?>