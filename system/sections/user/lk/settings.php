<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `notice_news`, `notice_help` FROM `users` WHERE `id`="'.$user['id'].'" LIMIT 1');
	$user = array_merge($user, $sql->get());

	if(isset($url['action']) AND in_array($url['action'], array('upload', 'news', 'help', 'important')))
	{
		switch($url['action'])
		{
			case 'upload':
				$file = isset($_POST['value']) ? $_POST['value'] : exit;
				$name = isset($_POST['name']) ? $_POST['name'] : exit;

				$pname = explode('.', $name);
				$type = strtolower(end($pname));

				if(!in_array($type, array('png', 'gif', 'jpg', 'bmp')))
					exit('Допустимый формат изображений: png, gif, jpg, bmp.');

				$aData = explode(',', $file);

				if(file_put_contents(ROOT.'upload/avatars/'.$user['id'].'.'.$type, base64_decode(str_replace(' ','+', $aData[1]))))
					exit($user['id'].':ok');

				exit('Ошибка загрузки: убедитесь, что изображение не повреждено и имеет правильный формат.');

			case 'news':
				$notice = $user['notice_news'] ? 0 : 1;

				$sql->query('UPDATE `users` set `notice_news`="'.$notice.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

				sys::outjs(array('s' => 'ok'));

			case 'help':
				$notice = $user['notice_help'] ? 0 : 1;

				$sql->query('UPDATE `users` set `notice_help`="'.$notice.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

				sys::outjs(array('s' => 'ok'));
		}
	}

	$html->get('settings', 'sections/user/lk');

		$html->set('id', $user['id']);
		$html->set('ava', users::ava($user['id']));

		if($user['notice_news'])
			$html->unit('notice_news', true);
		else
			$html->unit('notice_news');

		if($user['notice_help'])
			$html->unit('notice_help', true);
		else
			$html->unit('notice_help');

    $html->pack('main');
?>