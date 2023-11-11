<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Список подключенных серверов', $cfg['http'].'control');

	if(in_array($ctrl['status'], array('install', 'overdue', 'blocked')))
		include(SEC.'control/noaccess.php');
	else{
		if($go)
		{
			$game = isset($url['game']) ? $url['game'] : sys::outjs(array('e' => 'Необходимо указать игру'));

			if(!in_array($game, array('cs', 'cssold', 'css', 'csgo')))
				sys::outjs(array('e' => 'Указанная игра не найдена'));

			$sql->query('SELECT `address`, `passwd`, `limit` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
			$ctrl = $sql->get();

			$sql->query('SELECT `id` FROM `control_servers` WHERE `unit`="'.$id.'" LIMIT '.$ctrl['limit']);
			if($sql->num() == $ctrl['limit'])
				sys::outjs(array('e' => 'На данном тарифе нельзя установить больше, чем '.$ctrl['limit'].' шт. игровых серверов'));

			$ip = $ctrl['address'];
			$port = false;

			$port_min = 27015;

			// Проверка наличия свободного порта
			for($port_min; $port_min <= 30000; $port_min+=1)
			{
				$sql->query('SELECT `id` FROM `control_servers` WHERE `unit`="'.$id.'" AND `address`="'.$ip.':'.$port_min.'" LIMIT 1');
				if(!$sql->num())
				{
					$port = $port_min;

					break;
				}
			}

			$sql->query('INSERT INTO `control_servers` set '
				.'`unit`="'.$id.'",'
				.'`address`="'.$ip.':'.$port.'",'
				.'`game`="'.$game.'",'
				.'`slots`="32",'
				.'`status`="install", '.$cfg['control_install'][$game]);

			$uid = $sql->id()+1000;

			if(in_array($game, array('css', 'csgo')))
				$screen = 'cd '.$cfg['steamcmd'].'; ./steamcmd.sh +login anonymous +force_install_dir "/servers/'.$uid.'" +app_update '.$cfg['control_steamcmd'][$game].' +quit; cd /servers/'.$uid.';';
			else{
				$zip = array_shift(array_keys($cfg['control_packs'][$game])).'.zip';

				$screen = 'rm '.$zip.'; wget '.$cfg['control_server'].'/'.$zip.'; unzip -d . '.$zip.'; rm '.$zip.';';
			}

			include(LIB.'ssh.php');

			if(!$ssh->auth($ctrl['passwd'], $ctrl['address']))
				sys::outjs(array('e' => 'Неудалось создать связь с физическим сервером'));

			$ssh->set('mkdir /servers/'.$uid.';' // Создание директории
				.'useradd -s /bin/false -d /servers/'.$uid.' -g servers -u '.$uid.' server'.$uid.';' // Создание пользователя сервера на локации
				.'chown server'.$uid.':1000 /servers/'.$uid.';' // Изменение владельца и группы директории
				.'cd /servers/'.$uid.' && sudo -u server'.$uid.' screen -dmS i_'.$uid.' sh -c "'.$screen
				.'find . -type d -exec chmod 700 {} \;;'
				.'find . -type f -exec chmod 600 {} \;;'
				.'chmod 500 '.params::$aFileGame[$game].'"');

			$id = $uid-1000;

			$sql->query('UPDATE `control_servers` set `uid`="'.$uid.'" WHERE `id`="'.$id.'" LIMIT 1');

			sys::outjs(array('s' => 'ok', 'id' => $id));
		}

		$html->nav('Подключенный сервер #'.$id, $cfg['http'].'control/id/'.$id);
		$html->nav('Установка игрового сервера');

		$html->get('install', 'sections/control');
			$html->set('id', $id);
		$html->pack('main');
	}
?>