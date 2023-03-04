<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `unit`, `tarif`, `time_start` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = array_merge($server, $sql->get());

	if($go)
	{
		$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
		$unit = $sql->get();

		$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
		$tarif = $sql->get();

		include(LIB.'ssh.php');

		$command = isset($_POST['command']) ? sys::cmd($_POST['command']) : '';

		if($server['status'] == 'off')
		{
			if($command)
				sys::outjs(array('e' => sys::text('servers', 'off')));

			sys::out(sys::text('servers', 'off'));
		}

		if(!$ssh->auth($unit['passwd'], $unit['address']))
		{
			if($command)
				sys::outjs(array('e' => sys::text('error', 'ssh')));

			sys::out(sys::text('error', 'ssh'));
		}

		$dir = $tarif['install'].$server['uid'].'/csgo/';

		$filecmd = $dir.'console.log';

		if($command)
		{
			if(strtolower($command) == 'clear')
				$ssh->set('sudo -u server'.$server['uid'].' sh -c "echo \"Очистка консоли\n\" > '.$filecmd.'"');
			else
				$ssh->set('sudo -u server'.$server['uid'].' screen -p 0 -S s_'.$server['uid'].' -X eval \'stuff "'.$command.'"\015\';'
						.'sudo -u server'.$server['uid'].' screen -p 0 -S s_'.$server['uid'].' -X eval \'stuff \015\'');

			sys::outjs(array('s' => 'ok'));
		}

		$filecmd_copy = $dir.'oldstart/'.date('d.m.Y_H:i:s', $server['time_start']).'.log';

		$weight = sys::int($ssh->get('du --block-size=1 '.$filecmd.' | awk \'{print $1}\''));

		if($weight > 524288)
			$ssh->set('sudo -u server'.$server['uid'].' sh -c "mkdir -p '.$dir.'oldstart; cat '.$filecmd.' >> '.$filecmd_copy.'; echo \"Выполнена очистка консоли, слишком большой объем данных\n\" > '.$filecmd.'"');

		sys::out(htmlspecialchars($ssh->get('cat '.$filecmd), NULL, ''));
	}
	
	$html->nav($server['address'], $cfg['http'].'servers/id/'.$id);
	$html->nav('Консоль');
	
	$html->get('console', 'sections/servers/'.$server['game']);
		$html->set('id', $id);
	$html->pack('main');
?>