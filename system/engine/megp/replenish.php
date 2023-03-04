<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Проверка на авторизацию
	sys::noauth();

	if(isset($url['key']) && isset($url['sum']))
		sys::out(md5($cfg['freekassa_id'].':'.intval($url['sum']).':'.$cfg['freekassa_key_1'].':1'));

	// Генерация списка операций
	$qLog = $sql->query('SELECT `text`, `date` FROM `logs` WHERE `user`="'.$user['id'].'" AND `type`="replenish" ORDER BY `id` DESC LIMIT 5');
	while($aLog = $sql->get($qLog))
	{
		$html->get('list', 'sections/user/replenish');
			$html->set('text', $aLog['text']);
			$html->set('date', sys::today($aLog['date'], true));
		$html->pack('logs');
	}

	$html->get('replenish', 'sections/user');
		$html->set('id', $user['id']);
		$html->set('wmr', $cfg['webmoney_wmr']);
		$html->set('freekassa', $cfg['freekassa_id']);
		$html->set('unitpay', $cfg['unitpay_pubkey']);
		$html->set('balance', round($user['balance'], 2));
		$html->set('cur', $cfg['currency']);
		$html->set('logs', isset($html->arr['logs']) ? $html->arr['logs'] : '');
	$html->pack('main');
?>