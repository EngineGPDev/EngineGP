<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	switch($aWebInstall[$server['game']][$url['subsection']])
	{
		case 'server':
			$sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `server`="'.$id.'" LIMIT 1');

			break;

		case 'user':
			$sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" LIMIT 1');

			break;

		case 'unit':
			$sql->query('SELECT `domain`, `passwd`, `login`, `date` FROM `web` WHERE `type`="'.$url['subsection'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');

			break;
	}

	if(!$sql->num())
		sys::back($cfg['http'].'servers/id/'.$id.'/section/web/subsection/'.$url['subsection'].'/action/install');

	$web = $sql->get();

	$html->nav('Управление '.$aWebname[$url['subsection']]);

	$html->get('manage', 'sections/web/'.$url['subsection'].'/free');

		$html->set('id', $id);

		$html->set('url', $web['domain']);
		$html->set('login', $web['login']);
		$html->set('passwd', $web['passwd']);

	$html->pack('main');
?>