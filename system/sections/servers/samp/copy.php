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
				$nmch = sys::rep_act('server_copy_go_'.$id, 10);

			if($server['status'] != 'off' AND $url['subsection'] != 'remove')
				sys::outjs(array('e' => 'Игровой сервер должен быть выключен'), $nmch);

			$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
			$tarif = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
				sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

			include(SEC.'servers/games/copy/'.$url['subsection'].'.php');
		}
	}

	$html->nav($server['address'], $cfg['http'].'servers/id/'.$id);
    $html->nav('Резервные копии');

    if($mcache->get('server_copy_'.$id) != '')
        $html->arr['main'] = $mcache->get('server_copy_'.$id);
    else{
		// Построение списка создания копии
		foreach(params::$section_copy[$server['game']]['aCopy'] as $name => $info)
		{
			$html->get('list', 'sections/servers/games/copy');

				$html->set('name', $name);
				$html->set('info', $info);

			$html->pack('list');
		}

		// Построение списка созданных копий
		$sql->query('SELECT `id`, `server`, `info`, `date`, `status` FROM `copy` WHERE `user`="'.$server['user'].'_'.$server['unit'].'" AND `game`="'.$server['game'].'" ORDER BY `id` ASC');
		while($copy = $sql->get())
		{
			$html->get('copy', 'sections/servers/games/copy');

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

        $html->get('copy', 'sections/servers/'.$server['game']);

            $html->set('id', $id);

            $html->set('list', isset($html->arr['list']) ? $html->arr['list'] : '');
            $html->set('copy', isset($html->arr['copy']) ? $html->arr['copy'] : 'Резервные копии отсутствуют.');

        $html->pack('main');

        $mcache->set('server_copy_'.$id, $html->arr['main'], false, 4);
    }
?>