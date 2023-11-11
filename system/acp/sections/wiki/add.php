<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
		$aData['text'] = isset($_POST['text']) ? sys::bbc(trim($_POST['text'])) : '';
		$aData['cat'] = isset($_POST['cat']) ? sys::int($_POST['cat']) : '';
		$aData['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : '';

		if(in_array('', $aData))
			sys::outjs(array('e' => 'Необходимо заполнить все поля'));

		if(sys::strlen($aData['tags']) > 100)
			sys::outjs(array('e' => 'Теги не должен превышать 100 символов.'));

		$sql->query('SELECT `id` FROM `wiki_category` WHERE `id`="'.$aData['cat'].'" LIMIT 1');
		if(!$sql->num())
			sys::outjs(array('e' => 'Указанная категория не найдена'));

		$sql->query('INSERT INTO `wiki` set '
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`cat`="'.$aData['cat'].'",'
			.'`tags`="'.htmlspecialchars($aData['tags']).'",'
			.'`date`="'.$start_point.'"');

		$id = $sql->id();

		$sql->query('INSERT INTO `wiki_answer` set '
			.'`wiki`="'.$id.'",'
			.'`cat`="'.$aData['cat'].'",'
			.'`text`="'.htmlspecialchars($aData['text']).'"');

		sys::outjs(array('s' => 'ok'));
	}

	$cats = '';

	$sql->query('SELECT `id`, `name` FROM `wiki_category` ORDER BY `id` ASC');
	while($cat = $sql->get())
		$cats .= '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';

	$html->get('add', 'sections/wiki');

		$html->set('cats', $cats);

	$html->pack('main');
?>