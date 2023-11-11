<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($user['group'] == 'user')
		$sql->query('SELECT `id` FROM `help` WHERE `user`="'.$user['id'].'" AND `status`="0" AND `close`="0" LIMIT 1');
	else
		$sql->query('SELECT `id` FROM `help` WHERE `status`="1" AND `close`="0" LIMIT 1');

	if(!$sql->num())
		sys::outjs(array('empty' => ''));

	if($user['group'] != 'user')
	{
		$sql->query('SELECT `time` FROM `help` WHERE `status`="1" AND `close`="0" ORDER BY `time` DESC LIMIT 1');
		if($sql->num())
		{
			$help = $sql->get();

			sys::outjs(array('reply' => $help['time']));
		}

		sys::outjs(array('empty' => ''));
	}

	$help = $sql->get();

	$sql->query('SELECT `text`, `time` FROM `help_dialogs` WHERE `help`="'.$help['id'].'" AND `user`!="'.$user['id'].'" AND `time`>"'.($start_point-15).'" ORDER BY `id` DESC LIMIT 1');
	if(!$sql->num())
		sys::outjs(array('reply' => ''));

	$msg = $sql->get();
	
	if(strip_tags($msg['text'], '<br>,<p>') != $msg['text'])
		sys::outjs(array('reply' => ''));

	include(LIB.'help.php');

	$html->get('notice', 'sections/help');

		$html->set('id', $help['id']);
		$html->set('home', $cfg['http']);
		$html->set('text', $msg['text']);
		$html->set('ago', help::ago($msg['time']));

	$html->pack('notice');

	sys::outjs(array('notice' => $html->arr['notice']));
?>