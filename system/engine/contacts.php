<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'Контактная информация';

	$html->nav($title);

	$html->get('contacts');
	$html->pack('main');
?>