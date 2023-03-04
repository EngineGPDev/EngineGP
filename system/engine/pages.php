<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$id)
		include(ENG.'404.php');

	$sql->query('SELECT `name`, `file` FROM `pages` WHERE `id`="'.$id.'" LIMIT 1');

	if(!$sql->num())
		include(ENG.'404.php');

	$page = $sql->get();

	$title = $page['name'];

	$html->nav($page['name']);

	$html->get('page');

		$html->set('content', file_get_contents(FILES.'pages/'.$page['file']));

	$html->pack('main');
?>