<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	sys::noauth();

	$title = 'Главная страница';

	$qLog = $sql->query('SELECT `text`, `date` FROM `logs` WHERE `user`="'.$user['id'].'" ORDER BY `id` DESC LIMIT 5');
    while($aLog = $sql->get($qLog))
    {
        $html->get('list', 'logs');
            $html->set('text', $aLog['text']);
            $html->set('date', sys::today($aLog['date'], true));
        $html->pack('logs');
    }

	$html->get('index');
		$html->set('ip', $uip);
		$html->set('login', $user['login']);
		$html->set('balance', round($user['balance'], 2));
		$html->set('cur', $cfg['currency']);
		$html->set('logs', isset($html->arr['main']) ? $html->arr['main'] : '');
	$html->pack('main');
?>