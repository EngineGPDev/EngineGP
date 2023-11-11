<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($id)
	{
		$sql->query('SELECT `id`, `name`, `full_text`, `views`, `tags`, `date` FROM `news` WHERE `id`="'.$id.'" LIMIT 1');

		if(!$sql->num())
			include(ENG.'404.php');

		$news = $sql->get();

		$title = $news['name'];

		$sql->query('UPDATE `news` set `views`="'.($news['views']+1).'" WHERE `id`="'.$id.'" LIMIT 1');

		$html->get('news', 'sections/news');
			$html->set('id', $news['id']);
			$html->set('name', htmlspecialchars_decode($news['name']));
			$html->set('text', htmlspecialchars_decode($news['full_text']));
			$html->set('date', sys::today($news['date']));
		$html->pack('main');
	}else{
		$title = 'Последние новости';

		$sql->query('SELECT `id`, `name`, `text`, `views`, `tags`, `date` FROM `news` ORDER BY `id` DESC LIMIT 5');
		while($news = $sql->get())
		{
			$html->get('list', 'sections/news');
				$html->set('id', $news['id']);
				$html->set('name', htmlspecialchars_decode($news['name']));
				$html->set('text', htmlspecialchars_decode($news['text']));
				$html->set('date', sys::today($news['date']));
			$html->pack('news');
		}

		$html->get('index', 'sections/news');
			$html->set('list', isset($html->arr['news']) ? $html->arr['news'] : '');
		$html->pack('main');
	}
?>