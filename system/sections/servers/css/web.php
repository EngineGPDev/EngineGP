<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav($server['address'], $cfg['http'].'servers/id/'.$id);

	include(DATA.'web.php');

	// Если выбран подраздел
	if(isset($url['subsection']) AND in_array($url['subsection'], $aSub) AND in_array($url['action'], array_merge($aAction[$url['subsection']], array('install', 'manage'))))
	{
		if($go)
			$nmch = sys::rep_act('server_web_go_'.$id, 10);
		else
			$html->nav('Web', $cfg['http'].'servers/id/'.$id.'/section/web');

		include(SEC.'web/'.$url['subsection'].'/free/'.$url['action'].'.php');
	}else{
		$html->nav('Web');

		if($mcache->get('server_web_'.$id) != '')
			$html->arr['main'] = $mcache->get('server_web_'.$id);
		else{
			// Услуги
			foreach($aWeb[$server['game']] as $service => $active)
			{
				if($active)
				{
					if($service == 'hosting')
					{
						if(!$aWebVHTtype && !in_array($server['tarif'], $aWebVHT))
							continue;

						if($aWebVHTtype && in_array($server['tarif'], $aWebVHT))
							continue;
					}

					// Проверка на установку
					switch($aWebInstall[$server['game']][$service])
					{
						case 'server':
							$sql->query('SELECT `id` FROM `web` WHERE `type`="'.$service.'" AND `server`="'.$id.'" LIMIT 1');
							break;

						case 'user':
							$sql->query('SELECT `id` FROM `web` WHERE `type`="'.$service.'" AND `user`="'.$server['user'].'" LIMIT 1');
							break;

						case 'unit':
							$sql->query('SELECT `id` FROM `web` WHERE `type`="'.$service.'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');
					}

					if($sql->num())
						$html->get('list_install', 'sections/servers/games/web');
					else
						$html->get('list', 'sections/servers/games/web');

						$html->set('id', $id);
						$html->set('service', $service);
						$html->set('name', $aWebname[$service]);
						$html->set('desc', $aWebDesc[$service]);

					$html->pack($aWebType[$service]);
				}
			}

			// Блоки услуг
			foreach($aWebTypeInfo[$server['game']] as $type => $name)
			{
				if(!isset($html->arr[$type]))
					continue;
					
				$html->get('block', 'sections/servers/games/web');
					$html->set('name', $name);
					$html->set('list', $html->arr[$type]);
				$html->pack('web');
			}

			$html->get('web', 'sections/servers/'.$server['game']);
				$html->set('id', $id);
				$html->set('web', isset($html->arr['web']) ? $html->arr['web'] : 'Дополнительные услуги отсутствуют');
			$html->pack('main');

			$mcache->set('server_web_'.$id, $html->arr['main'], false, 4);
		}
	}
?>