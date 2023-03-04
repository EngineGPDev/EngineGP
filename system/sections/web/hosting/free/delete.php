<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	if($user['group'] != 'admin')
		sys::outjs(array('i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'), $nmch);

	// Проверка на наличие установленной услуги
	switch($aWebInstall[$server['game']][$url['subsection']])
	{
		case 'server':
			$sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `server`="'.$id.'" LIMIT 1');
			break;

		case 'user':
			$sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" LIMIT 1');
			break;

		case 'unit':
			$sql->query('SELECT `id`, `login` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');
			break;
	}

	if(!$sql->num())
		sys::outjs(array('i' => 'Дополнительная услуга не установлена.'), $nmch);

	$web = $sql->get();

	// Удаление вирт. хостинга
	$result = json_decode(file_get_contents(sys::updtext($aWebUnit['isp']['account']['delete'], array('login' => $web['login']))), true);

	if(!isset($result['result']) || strtolower($result['result']) != 'ok')
		sys::outjs(array('e' => 'Не удалось удалить виртуальный хостинг.'), $nmch);

	// Обновление данных
	$sql->query('DELETE FROM `web` WHERE `id`="'.$web['id'].'" LIMIT 1');

	sys::outjs(array('s' => 'ok'), $nmch);
?>