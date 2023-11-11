<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Список новостей', $cfg['http'].'news');

	$sql->query('SELECT `id`, `name`, `full_text`, `views`, `tags`, `date` FROM `news` WHERE `id`="'.$id.'" LIMIT 1');

	if(!$sql->num())
		include(ENG.'404.php');

	$news = $sql->get();

	$sql->query('UPDATE `news` set `views`="'.($news['views']+1).'" WHERE `id`="'.$id.'" LIMIT 1');

	$text = htmlspecialchars_decode($news['full_text']);

	$title = $news['name'];
	$description = $text;
	$keywords = $news['tags'];

	$html->nav($news['name']);

	$html->get('news', 'sections/news');

		$html->set('id', $news['id']);
		$html->set('name', htmlspecialchars_decode($news['name']));
		$html->set('text', $text);
		$html->set('views', $news['views']);
		$html->set('tags', sys::tags($news['tags']));
		$html->set('date', sys::today($news['date']));

	$html->pack('main');
?>