<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));
	
	if(isset($section))
	{
		switch($section)
		{
			case 'send':
				if(empty($_POST['text']))
					sys::outjs(array('e' => 'Необходимо написать сообщение'));
				
				$msg = $_POST['text'];
				$sql->query('INSERT INTO `chat` (`userid`, `date`, `msg`) VALUES ("'.$user['id'].'", NOW(), "'.mysqli_real_escape_string($sql->sql_id, $msg).'");');
				sys::outjs(array('s' => ''));
			case 'dialog':
				$q_Msgs = $sql->query('SELECT `chat`.`id`, `userid`, `msg`, `chat`.`date`, `login`, `group` FROM `chat` INNER JOIN `users` ON `chat`.`userid` = `users`.`id` ORDER BY `chat`.`date` ASC LIMIT 30');
				while($msg = $sql->get($q_Msgs))
				{
					$html->get('messages_all', 'sections/chat');
						if($msg['group'] == 'admin')
							$group = '<span style="color: #f94747">Администратор</span>';
						else if($msg['group'] == 'support')
							$group = '<span style="color: #00ff8a">Тех. Поддержка</span>';
						else if($msg['group'] == 'user')
							$group = '<span>Клиент</span>';
						
						$name = $msg['login'].' ('.$group.')';
					
						$html->set('id', $msg['id']);
						$html->set('userid', $msg['userid']);
						$html->set('name', $name);
						$html->set('time', $time);
						$html->set('date', $msg['date']);
						$html->set('msg', $msg['msg']);
						$html->set('login', $msg['login']);
						
						$html->unit('me', $user['id'] == $userid, 1);
					
						for($i = 1; $i <= 32; $i++)
							$html->set('emoji_'.$i, '<span class="emoji" data-value="emoji_'.$i.'"></span>');
						
		global $cfg;
		$file = 'upload/avatars/' . $resp['uid'] . '.';
		$link = $cfg['http'] . 'upload/avatars/' . $resp['uid'] . '.';
		if (file_exists(ROOT . $file . 'jpg'))
			$html->set('ava', '/upload/avatars/' . $resp['uid'] . '.jpg');
		elseif (file_exists(ROOT . $file . 'png'))
			$html->set('ava', '/upload/avatars/' . $resp['uid'] . '.png');
		elseif (file_exists(ROOT . $file . 'gif'))
			$html->set('ava', '/upload/avatars/' . $resp['uid'] . '.gif');
		else
			$html->set('ava', $cfg['http'] . 'template/images/avatar.png');
					$html->pack('dialog');
				}
				sys::out(isset($html->arr['dialog']) ? $html->arr['dialog'] : '');
			case 'delete':
				if($user['group'] != 'admin')
					sys::outjs(array('e' => 'Недостаточно прав'));
				
				if(!isset($url['id']))
					sys::outjs(array('e' => 'Отсутствует идентификатор'));
				
				if($go)
					$sql->query('DELETE FROM `chat` WHERE `userid`="'.$url['id'].'";');
				else
					$sql->query('DELETE FROM `chat` WHERE `id`="'.$url['id'].'";');
				
				sys::outjs(array('s' => ''));
		}
	}

	$html->nav($title);

	$q_Msgs = $sql->query('SELECT `chat`.`id`, `userid`, `msg`, `chat`.`date`, `login`, `group` FROM `chat` INNER JOIN `users` ON `chat`.`userid` = `users`.`id` ORDER BY `chat`.`date` ASC LIMIT 30');
	while($msg = $sql->get($q_Msgs))
	{
        $html->get('messages_all', 'sections/chat');
			if($msg['group'] == 'admin')
				$group = '<span style="color: #f94747">Администратор</span>';
			else if($msg['group'] == 'support')
				$group = '<span style="color: #00ff8a">Тех. Поддержка</span>';
			else if($msg['group'] == 'user')
				$group = '<span>Клиент</span>';
			
			$name = $msg['login'].' ('.$group.')';
		
        	$html->set('id', $msg['id']);
	    	$html->set('userid', $msg['userid']);
	    	$html->set('name', $name);
	    	$html->set('time', $time);
	    	$html->set('date', $msg['date']);
	    	$html->set('msg', $msg['msg']);
			$html->set('login', $msg['login']);
			
	    	$html->unit('me', $user['id'] == $userid, 1);
        
            for($i = 1; $i <= 32; $i++)
				$html->set('emoji_'.$i, '<span class="emoji" data-value="emoji_'.$i.'"></span>');
	    	
			global $cfg;
			$file = 'upload/avatars/' . $msg['uid'] . '.';
			$link = $cfg['http'] . 'upload/avatars/' . $msg['uid'] . '.';
			if (file_exists(ROOT . $file . 'jpg'))
				$html->set('ava', '/upload/avatars/' . $msg['uid'] . '.jpg');
			elseif (file_exists(ROOT . $file . 'png'))
				$html->set('ava', '/upload/avatars/' . $msg['uid'] . '.png');
			elseif (file_exists(ROOT . $file . 'gif'))
				$html->set('ava', '/upload/avatars/' . $msg['uid'] . '.gif');
			else
				$html->set('ava', $cfg['http'] . 'template/images/avatar.png');
        $html->pack('msg_all');
	}
	
	$html->get('dialog', 'sections/chat');
		$html->set('chats', isset($html->arr['msg_all']) ? $html->arr['msg_all'] : '');
	$html->pack('main');
?>