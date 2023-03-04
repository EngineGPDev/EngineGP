<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Категории вопросов');

	$cats = $sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `sort` ASC');
	while($cat = $sql->get($cats))
	{
		$sql->query('SELECT `id` FROM `wiki` WHERE `cat`="'.$cat['id'].'" LIMIT 1');
		if(!$sql->num())
			continue;

		$html->get('list', 'sections/wiki/category');

			$html->set('id', $cat['id']);
			$html->set('name', $cat['name']);

		$html->pack('category');
	}

	$html->get('category', 'sections/wiki');

		$html->set('list', isset($html->arr['category']) ? $html->arr['category'] : '');

	$html->pack('main');
?>