<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$cats = $sql->query('SELECT `id`, `name`, `sort` FROM `wiki_category` ORDER BY `id` ASC');
	while($cat = $sql->get($cats))
	{
		$sql->query('SELECT `name` FROM `wiki` WHERE `cat`="'.$cat['id'].'"');
		$wiki = $sql->num();

		$list .= '<tr>';
			$list .= '<td>'.$cat['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/wiki/section/cat/id/'.$cat['id'].'">'.$cat['name'].'</a></td>';
			$list .= '<td class="text-center">'.$wiki.' шт.</td>';
			$list .= '<td class="text-center">'.$cat['sort'].'</td>';
			$list .= '<td><a href="#" onclick="return wiki_cat_delete(\''.$cat['id'].'\')" class="text-red">Удалить</a></td>';
		$list .= '</tr>';
	}

	$html->get('cats', 'sections/wiki');

		$html->set('list', $list);

	$html->pack('main');
?>