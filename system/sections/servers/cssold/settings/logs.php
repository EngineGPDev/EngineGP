<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    $html->nav('Логи');

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::back($cfg['http'].'servers/id/'.$id.'/section/settings');

	// Путь к логам
	$folder = $tarif['install'].$server['uid'].'/cstrike/logs';

	// Если выбран лог
	if(isset($url['log']))
	{
		if(sys::valid($url['log'], 'other', $aValid['cslogs']))
			sys::back($cfg['http'].'servers/id/'.$id.'/section/settings/subsection/logs');

		$ssh->set('sudo -u server'.$server['uid'].' cat '.$folder.'/'.$url['log']);

		$html->get('view', 'sections/servers/games/settings/logs');

				$html->set('id', $id);
				$html->set('name', $url['log']);
				$html->set('log', htmlspecialchars($ssh->get()));
				$html->set('uri', 'logs');

		$html->pack('main');
	}else{
		if(isset($url['delall']))
		{
			$ssh->set('cd '.$folder.' && rm *.log');

			sys::back($cfg['http'].'servers/id/'.$id.'/section/settings/subsection/logs');
		}

		$ssh->set('cd '.$folder.' && du -ab --time | grep -e .log$ | awk \'{print $2" "$3"\/"$1"\/"$4}\' | sort -Mr');

		// Массив данных
		$aData = explode("\n", $ssh->get());

		if(isset($aData[count($aData)-1]))
			unset($aData[count($aData)-1]);

		// Построение списка
		foreach($aData as $line => $log)
		{
			$aLog = explode('\/', $log);

			// Название
			$name = explode('/', $aLog[2]);

			if(count($name) > 2)
				continue;

			// Дата
			$date = sys::unidate($aLog[0]);

			// Вес
			$size = sys::size($aLog[1]);

			$html->get('list', 'sections/servers/games/settings/logs');

				$html->set('id', $id);
				$html->set('name', end($name));
				$html->set('uri', 'logs/log/'.end($name));
				$html->set('date', $date);
				$html->set('size', $size);

			$html->pack('logs');
		}

		$html->get('logs', 'sections/servers/games/settings');

			$html->set('id', $id);
			$html->set('uri', '');
			$html->set('logs', isset($html->arr['logs']) ? $html->arr['logs'] : '');

		$html->pack('main');
	}
?>