<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `name`, `text`, `full_text`, `tags` FROM `news` WHERE `id`="'.$id.'" LIMIT 1');
	$news = $sql->get();

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : htmlspecialchars_decode($news['name']);
		$aData['text'] = isset($_POST['text']) ? sys::bbc(trim($_POST['text'])) : htmlspecialchars_decode($news['text']);
		$aData['full'] = isset($_POST['full']) ? sys::bbc(trim($_POST['full'])) : htmlspecialchars_decode($news['full_text']);
		$aData['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : htmlspecialchars_decode($news['tags']);

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		if(sys::strlen($aData['name']) > 50)
			sys::outjs(array('e' => 'Заголовок не должен превышать 50 символов.'));

		if(sys::strlen($aData['tags']) > 100)
			sys::outjs(array('e' => 'Теги не должен превышать 100 символов.'));

		$sql->query('UPDATE `news` set '
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`text`="'.htmlspecialchars($aData['text']).'",'
			.'`full_text`="'.htmlspecialchars($aData['full']).'",'
			.'`tags`="'.htmlspecialchars($aData['tags']).'" WHERE `id`="'.$id.'" LIMIT 1');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('news', 'sections/news');

		$html->set('id', $id);
		$html->set('name', htmlspecialchars_decode($news['name']));
		$html->set('text', htmlspecialchars_decode($news['text']));
		$html->set('full', htmlspecialchars_decode($news['full_text']));
		$html->set('tags', htmlspecialchars_decode($news['tags']));

	$html->pack('main');
?>