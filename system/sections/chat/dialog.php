<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));
	
	if(!$section || !in_array($section, array('dialog')))
		sys::back($cfg['http']);
	
	$q_Msgs = $sql->query('SELECT * FROM (SELECT `id`, `userid`, `msg`, `date` FROM `chat` ORDER BY `date` DESC LIMIT 30) t ORDER BY `date` ASC;');
	while($msg = $sql->get($q_Msgs))
	{
		if($user['group'] == 'admin')
			$group = '<span style="color: #f94747">Администратор</span>';
		//elseif ()
		//	$group = '<span style="color: #03aa46">(Клиент, серверов: 1 шт.)</span>';
		else
			$group = '<span>Клиент</span>';
		
		$name = $user['login'].' ('.$group.')'; // <span style="color: #f94747">(Администратор)</span>
		// $time = '[date]';
		
		$html->get('message', 'chatwin');

			$html->set('id', $msg['id']);
			$html->set('userid', $msg['userid']);
			$html->set('name', $name);
			$html->set('time', $time);
			$html->set('date', $msg['date']);
			$html->set('msg', $msg['msg']);

			$html->unit('me', $user['id'] == $userid, 1);

			for($i = 1; $i <= 32; $i++)
				$html->set('emoji_'.$i, '<span class="emoji" data-value="emoji_'.$i.'"></span>');

		$html->pack('dialog');
	}

	sys::out(isset($html->arr['dialog']) ? $html->arr['dialog'] : '');
?>