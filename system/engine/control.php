<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Проверка на авторизацию
	sys::noauth();

	$title = 'Список подключенных серверов';

	include(LIB.'control/control.php');

	if($id)
	{
		if($user['group'] == 'admin')
			$sql->query('SELECT `id`, `user`, `status`, `time` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
		else
			$sql->query('SELECT `id`, `user`, `status`, `time` FROM `control` WHERE `id`="'.$id.' AND `user`="'.$user['id'].'" LIMIT 1');

		if(!$sql->num())
		{
			if($go)
				sys::outjs(array('e' => 'Сервер #'.$id.' не найден'));

			sys::back($cfg['http'].'control');
		}

		$ctrl = $sql->get();

		if(in_array($ctrl['status'], array('install', 'overdue', 'blocked', 'reboot')) && !in_array($section, array('extend', 'scan')))
			include(SEC.'control/noaccess.php');
		else{
			if(!$section)
				$section = 'index';

			$sid = array_key_exists('server', $url) ? sys::int($url['server']) : false;

			if($sid)
				include(SEC.'control/servers/'.$section.'.php');
			else
				include(SEC.'control/'.$section.'.php');
		}
	}else{
		$html->nav($title);

		$js = '';

		$ctrls = $sql->query('SELECT `id`, `address`, `passwd`, `time`, `date`, `status` FROM `control` WHERE `user`="'.$user['id'].'"');
		while($ctrl = $sql->get($ctrls))
		{
			$time_end = $ctrl['status'] == 'overdue' ? 'Удаление через: '.sys::date('min', $ctrl['overdue']+$cfg['control_delete']*86400) : 'Осталось: '.sys::date('min', $ctrl['time']);
			$btn = ctrl::buttons($ctrl['id'], $ctrl['status']);

			$html->get('control', 'sections/control');
				$html->set('id', $ctrl['id']);
				$html->set('address', $ctrl['address']);
				$html->set('passwd', $ctrl['passwd']);
				$html->set('time', sys::today($ctrl['time']));
				$html->set('date', sys::today($ctrl['date']));
				$html->set('time_end', $time_end);
				$html->set('status', ctrl::status($ctrl['status']));
				$html->set('btn', $btn);
			$html->pack('list');

			$js .= 'update_resources('.$ctrl['id'].', true);';
		}

		$html->get('controls', 'sections/control');
			$html->set('list', isset($html->arr['list']) ? $html->arr['list'] : 'У вас нет подключенных серверов', true);
			$html->set('updates_control', $js);
		$html->pack('main');
	}
?>