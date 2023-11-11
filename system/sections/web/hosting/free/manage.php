<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$options = '';

	switch($aWebInstall[$server['game']][$url['subsection']])
	{
		case 'server':
			$sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `server`="'.$id.'" LIMIT 1');

			$options = '<option value="'.$id.'">#'.$id.' '.$server['name'].' ('.$server['address'].')</option>';

			break;

		case 'user':
			$sql->query('SELECT `id`, `address`, `name` FROM `servers` WHERE `user`="'.$server['user'].'" AND (`status`!="overdue" OR `status`!="block")');
			while($sers = $sql->get())
				$options .= '<option value="'.$sers['id'].'">#'.$sers['id'].' '.$sers['name'].' ('.$sers['address'].')</option>';

			$sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" LIMIT 1');

			break;

		case 'unit':
			$sql->query('SELECT `id`, `address`, `name` FROM `servers` WHERE `unit`="'.$server['unit'].'" AND `user`="'.$server['user'].'" AND (`status`!="overdue" OR `status`!="block")');
			while($sers = $sql->get())
				$options .= '<option value="'.$sers['id'].'">#'.$sers['id'].' '.$sers['name'].' ('.$sers['address'].')</option>';

			$sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');

			break;
	}

	if(!$sql->num())
		sys::back($cfg['http'].'servers/id/'.$id.'/section/web/subsection/'.$url['subsection'].'/action/install');

	$web = $sql->get();

	$html->nav('Управление '.$aWebname[$url['subsection']]);

	$html->get('manage', 'sections/web/'.$url['subsection'].'/free');

		$html->set('id', $id);

		$html->set('url', $aWebUnit['isp']['panel']);
		$html->set('login', $web['login']);
		$html->set('passwd', $web['passwd']);
		$html->set('date', sys::today($web['date']));

	$html->pack('main');
?>