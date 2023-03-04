<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Проверка на авторизацию
	sys::noauth();

	$sql->query('SELECT `id` FROM `users` WHERE `id`="'.$user['id'].'" AND `help`="0" LIMIT 1');
	if(!$sql->num())
	{
		$html->get('noaccess', 'sections/help');
		$html->pack('main');
	}else{
		// Подключение раздела
		if(!in_array($section, array('create', 'dialog', 'open', 'close', 'notice', 'upload')))
			include(ENG.'404.php');

		$aNav = array(
			'help' => 'Техническая поддержка',
			'create' => 'Создание вопроса',
			'dialog' => 'Решение вопроса',
			'open' => 'Список открытых вопросов',
			'close' => 'Список закрытых вопросов'
		);

		$title = isset($aNav[$section]) ? $aNav[$section] : $section;

		include(SEC.'help/'.$section.'.php');
	}
?>