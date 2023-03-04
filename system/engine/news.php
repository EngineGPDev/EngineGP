<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'Список новостей';

	if($id)
		include(SEC.'news/news.php');
	else{
		if((array_key_exists('page', $url) && count($url) != 1) || (!array_key_exists('page', $url) && count($url)))
			include(ENG.'404.php');

		include(SEC.'news/index.php');
	}
?>