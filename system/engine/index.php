<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'Главная страница';
	$html->nav($title);

	$sql->query('SELECT `id`, `name`, `text`, `views`, `tags`, `date` FROM `news` ORDER BY `id` DESC LIMIT 3');
	while($news = $sql->get())
	{
		$html->get('list', 'sections/news');

			$html->set('id', $news['id']);
			$html->set('name', htmlspecialchars_decode($news['name']));
			$html->set('text', htmlspecialchars_decode($news['text']));
			$html->set('views', $news['views']);
			$html->set('tags', sys::tags($news['tags']));
			$html->set('date', sys::today($news['date']));

		$html->pack('news');
	}

	$html->get('index');

		$html->set('news', isset($html->arr['news']) ? $html->arr['news'] : '');
	$html->pack('main');
?>