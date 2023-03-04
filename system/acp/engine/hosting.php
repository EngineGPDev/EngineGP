<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-globe"></i> Список вирт. хостингов';

	$html->get('menu', 'sections/hosting');
	$html->pack('menu');
?>