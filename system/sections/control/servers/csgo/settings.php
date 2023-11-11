<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `pack` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
	$server = array_merge($server, $sql->get());

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Список игровых серверов #'.$id, $cfg['http'].'control/id/'.$id);
	$html->nav($server['address'], $cfg['http'].'control/id/'.$id.'/server/'.$sid);

	$aSub = array('start', 'server', 'admins', 'bans', 'firewall', 'crontab', 'startlogs', 'debug', 'logs', 'smlogs', 'pack', 'file');

	// Если выбран подраздел
	if(isset($url['subsection']) AND in_array($url['subsection'], $aSub))
	{
		$html->nav('Настройки', $cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/settings');

		if($go)
			$nmch = sys::rep_act('ctrl_server_settings_go_'.$sid, 10);

		if(in_array($url['subsection'], $aRouteSub['settings']))
			include(SEC.'control/servers/games/settings/'.$url['subsection'].'.php');
		else
			include(SEC.'control/servers/'.$server['game'].'/settings/'.$url['subsection'].'.php');
	}else{
		$html->nav('Настройки');

		if($mcache->get('ctrl_server_settings_'.$sid) != '')
			$html->arr['main'] = $mcache->get('ctrl_server_settings_'.$sid);
		else{
			$aEditslist = 1;
			$ctrlmod = true;
			include(DATA.'filedits.php');

			// Построение списка доступных сборок
			$aPacks = $cfg['control_packs'][$server['game']];

			$packs = '<option value="'.$server['pack'].'">'.$aPacks[$server['pack']].'</option>';
			unset($aPacks[$server['pack']]);

			foreach($aPacks as $pack => $desc)
				$packs .= '<option value="'.$pack.'">'.$desc.'</option>';

			include(SEC.'control/servers/'.$server['game'].'/settings/start.php');

			$html->get('settings', 'sections/control/servers/'.$server['game']);
				$html->set('id', $id);
				$html->set('server', $sid);
				$html->set('packs', $packs);
				$html->set('start', $html->arr['start']);
				if(isset($html->arr['edits']))
				{
					$html->set('edits', $html->arr['edits']);
					$html->unit('edits', 1);
				}else
					$html->unit('edits');
			$html->pack('main');

			$mcache->set('ctrl_server_settings_'.$sid, $html->arr['main'], false, 60);
		}
	}
?>