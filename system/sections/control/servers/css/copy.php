<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		// Подразделы
		$aSub = array('fullcopy', 'create', 'recfull', 'recpart', 'remove', 'check');

		// Если выбран подраздел
		if(isset($url['subsection']) AND in_array($url['subsection'], $aSub))
		{
			if($url['subsection'] != 'check')
				$nmch = sys::rep_act('ctrl_server_copy_go_'.$sid, 10);

			if($server['status'] != 'off' AND $url['subsection'] != 'remove')
				sys::outjs(array('e' => 'Игровой сервер должен быть выключен'), $nmch);

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
				sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

			include(SEC.'control/servers/games/copy/'.$url['subsection'].'.php');
		}
	}

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Список игровых серверов #'.$id, $cfg['http'].'control/id/'.$id);
	$html->nav($server['address'], $cfg['http'].'control/id/'.$id.'/server/'.$sid);
    $html->nav('Резервные копии');

    if($mcache->get('ctrl_server_copy_'.$sid) != '')
        $html->arr['main'] = $mcache->get('ctrl_server_copy_'.$sid);
    else{
		// Построение списка создания копии
		foreach(params::$section_copy[$server['game']]['aCopy'] as $name => $info)
		{
			$html->get('list', 'sections/control/servers/games/copy');

				$html->set('name', $name);
				$html->set('info', $info);

			$html->pack('list');
		}

		// Построение списка созданных копий
		$sql->query('SELECT `id`, `server`, `info`, `date`, `status` FROM `control_copy` WHERE `user`="'.$ctrl['user'].'_'.$id.'" AND `game`="'.$server['game'].'" ORDER BY `id` ASC');
		while($copy = $sql->get())
		{
			$html->get('copy', 'sections/control/servers/games/copy');

				$html->set('id', $copy['id']);
				$html->set('info', $copy['info']);
				$html->set('server', $copy['server']);
				$html->set('date', sys::today($copy['date']));

				if($copy['status'])
				{
					$html->unit('created', 1);
					$html->unit('!created');
				}else{
					$html->unit('created');
					$html->unit('!created', 1);
				}

			$html->pack('copy');
		}

        $html->get('copy', 'sections/control/servers/'.$server['game']);

            $html->set('id', $id);
            $html->set('server', $sid);

            $html->set('list', isset($html->arr['list']) ? $html->arr['list'] : '');
            $html->set('copy', isset($html->arr['copy']) ? $html->arr['copy'] : 'Резервные копии отсутствуют.');

        $html->pack('main');

        $mcache->set('ctrl_server_copy_'.$sid, $html->arr['main'], false, 4);
    }
?>