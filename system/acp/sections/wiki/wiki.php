<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `name`, `cat`, `tags` FROM `wiki` WHERE `id`="'.$id.'" LIMIT 1');
	$wiki = $sql->get();
	
	$sql->query('SELECT `text` FROM `wiki_answer` WHERE `wiki`="'.$id.'" LIMIT 1');
	$wiki = array_merge($wiki, $sql->get());

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : htmlspecialchars_decode($wiki['name']);
		$aData['text'] = isset($_POST['text']) ? sys::bbc(trim($_POST['text'])) : htmlspecialchars_decode($wiki['text']);
		$aData['cat'] = isset($_POST['cat']) ? sys::int($_POST['cat']) : $wiki['cat'];
		$aData['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : htmlspecialchars_decode($wiki['tags']);

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		if(sys::strlen($aData['tags']) > 100)
			sys::outjs(array('e' => 'Теги не должен превышать 100 символов.'));

		$sql->query('SELECT `id` FROM `wiki_category` WHERE `id`="'.$aData['cat'].'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Указанная категория не найдена'));

		$sql->query('UPDATE `wiki` set '
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`cat`="'.$aData['cat'].'",'
			.'`tags`="'.htmlspecialchars($aData['tags']).'",'
			.'`date`="'.$start_point.'" WHERE `id`="'.$id.'" LIMIT 1');

		$sql->query('UPDATE `wiki_answer` set '
			.'`cat`="'.$aData['cat'].'",'
			.'`text`="'.htmlspecialchars($aData['text']).'" WHERE `wiki`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	$cats = '';

	$sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `id` ASC');
	while($cat = $sql->get())
		$cats .= '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';

	$html->get('wiki', 'sections/wiki');

		$html->set('id', $id);
		$html->set('name', htmlspecialchars_decode($wiki['name']));
		$html->set('text', htmlspecialchars_decode($wiki['text']));
		$html->set('tags', htmlspecialchars_decode($wiki['tags']));
		$html->set('cats', str_replace('"'.$wiki['cat'].'"', '"'.$wiki['cat'].'" selected', $cats));

	$html->pack('main');
?>