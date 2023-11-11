<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Генерация списка операций
    $qLog = $sql->query('SELECT `text`, `date` FROM `logs` WHERE `user`="'.$user['id'].'" ORDER BY `id` DESC LIMIT 40');
    while($aLog = $sql->get($qLog))
    {
        $html->get('list', 'sections/user/lk/logs');
            $html->set('text', $aLog['text']);
            $html->set('date', sys::today($aLog['date'], true));
        $html->pack('logs');
    }

	$html->get('logs', 'sections/user/lk');

		$html->set('logs', isset($html->arr['logs']) ? $html->arr['logs'] : 'Нет логов операций', true);

    $html->pack('main');
?>