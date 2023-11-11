<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `address`, `game`, `status` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
	$server = $sql->get();

	ctrl::nav($server, $id, $sid, 'filetp');

	$frouter = explode('/', ctrl::route($server, 'filetp', $go));

	if(end($frouter) == 'noaccess.php')
		include(SEC.'control/servers/noaccess.php');
	else{
		$sql->query('SELECT `uid`, `ftp`, `ftp_passwd` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
		$server = array_merge($server, $sql->get());

		$sql->query('SELECT `address` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
		$unit = $sql->get();
		$ip = sys::first(explode(':', $unit['address']));

		$html->nav('Список подключенных серверов', $cfg['http'].'control');
		$html->nav('Список игровых серверов #'.$id, $cfg['http'].'control/id/'.$id);
		$html->nav($server['address'], $cfg['http'].'control/id/'.$id.'/server/'.$sid);
		$html->nav('FileTP');

		// Путь для Proftpd
		$homedir = '/servers/'.$server['uid'];

		// Путь для файлового менеджера
		$dir = '/';

		$aData = array(
			'root' => $dir,
			'host' => $ip,
			'login' => $server['uid'],
			'passwd' => $server['ftp_passwd']
		);

		if($go)
		{
			if(isset($url['action']) AND in_array($url['action'], array('on', 'off', 'change', 'logs')))
			{
				$sql->query('SELECT `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
				$unit = array_merge($unit, $sql->get());

				include(LIB.'ssh.php');

				// Проверка соединения с ssh сервером
				if(!$ssh->auth($unit['passwd'], $unit['address']))
					sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');
			}else{
				include(LIB.'ftp.php');

				$ftp = new ftp;

				// Проверка соединения с ftp сервером
				if(!$ftp->auth($aData['host'], $aData['login'], $aData['passwd']))
				{
					if(isset($url['action']))
					{
						if($url['action'] == 'search')
								sys::out('Не удалось соединиться с ftp-сервером.');

						sys::outjs(array('e' => 'Не удалось соединиться с ftp-сервером.'));
					}

					sys::out();
				}
			}

			// Выполнение операций
			if(isset($url['action']))
				switch($url['action'])
				{
					case 'on':
						if($server['ftp'])
							sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');

						$used = sys::int($ssh->get('cd /servers/'.$server['uid'].' && du -b | tail -1'));

						if($used < 1)
							sys::back($cfg['http'].'help/action/create');

						$bytes = $server['hdd']*1048576;

						$server['ftp_passwd'] = isset($server['ftp_passwd']{1}) ? $server['ftp_passwd'] : sys::passwd(8);

						$qSql = 'DELETE FROM users WHERE username=\''.$server['uid'].'\';'
								.'INSERT INTO users set username=\''.$server['uid'].'\', password=\''.$server['ftp_passwd'].'\', uid=\''.$server['uid'].'\', gid=\'1000\', homedir=\''.$homedir.'\', shell=\'/bin/false\';';

						$ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e "'.$qSql.'"');

						$sql->query('UPDATE `control_servers` SET `ftp`="1", `ftp_passwd`="'.$server['ftp_passwd'].'" WHERE `id`="'.$sid.'" LIMIT 1');

						$mcache->delete('ctrl_server_filetp_'.$sid);

						sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');

					case 'change':
						if(!$server['ftp'])
							sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');

						$passwd = sys::passwd(8);

						$qSql = "UPDATE users set password='".$passwd."' WHERE username='".$server['uid']."' LIMIT 1";

						$ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e '.'"'.$qSql.'"');

						$sql->query('UPDATE `control_servers` SET `ftp_passwd`="'.$passwd.'" WHERE `id`="'.$sid.'" LIMIT 1');

						$mcache->delete('ctrl_server_filetp_'.$sid);

						sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');

					case 'off':
						if(!$server['ftp'])
							sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');

						$qSql = 'DELETE FROM users WHERE username=\''.$server['uid'].'\';';

						$ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e "'.$qSql.'"');

						$sql->query('UPDATE `control_servers` SET `ftp`="0" WHERE `id`="'.$sid.'" LIMIT 1');

						$mcache->delete('ctrl_server_filetp_'.$sid);

						sys::back($cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/filetp');

					case 'rename':
						$ftp->rename(json_decode($_POST['path']), json_decode($_POST['name']), json_decode($_POST['newname']));

					case 'edit':
						$ftp->edit_file(json_decode($_POST['path']), json_decode($_POST['name']));

					case 'create':
						if(isset($url['folder']))
							$ftp->mkdir(json_decode($_POST['path']), json_decode($_POST['name']));

						$ftp->touch(json_decode($_POST['path']), json_decode($_POST['name']), json_decode($_POST['text']));

					case 'delete':
						if(isset($url['folder']))
							$ftp->rmdir(json_decode($_POST['path']), json_decode($_POST['name']));

						$ftp->rmfile(json_decode($_POST['path']).'/'.json_decode($_POST['name']));

					case 'chmod':
						$ftp->chmod(json_decode($_POST['path']), json_decode($_POST['name']), sys::int($_POST['chmod']));

					case 'search':
						$text = isset($_POST['find']) ? sys::first(explode('.', json_decode($_POST['find']))) : sys::out();

						if(!isset($text{2}))
							sys::out('Для выполнения поиска, необходимо больше данных');

						$ftp->search($text, $id);

					case 'logs':
						$logs = $mcache->get('ctrl_filetp_logs_'.$sid);

						if(!$logs)
						{
							include(LIB.'ftp.php');

							$ftp = new ftp;

							$logs = $ftp->logs($ssh->get('cat /var/log/proftpd/xferlog | grep "/'.$server['uid'].'/" | awk \'{print $2"\\\"$3"\\\"$4"\\\"$5"\\\"$7"\\\"$8"\\\"$9"\\\"$12}\' | tail -50'), $server['uid']);

							$mcache->set('ctrl_filetp_logs_'.$sid, $logs, false, 300);
						}

						sys::out($logs);
				}

			if(!isset($_POST['path'])) $_POST['path'] = json_encode($aData['root']);

			sys::out($ftp->view($ftp->read(json_decode($_POST['path'])), $sid));
		}

		if($mcache->get('ctrl_server_filetp_'.$sid) != '')
			$html->arr['main'] = $mcache->get('ctrl_server_filetp_'.$sid);
		else{
			if($server['ftp'])
			{
				$html->get('filetp_on', 'sections/control/servers/games/filetp');

					$html->set('address', 'ftp://'.$aData['login'].':'.$aData['passwd'].'@'.$aData['host']);
					$html->set('server', $aData['host']);
					$html->set('login', $aData['login']);
					$html->set('passwd', $aData['passwd']);
					$html->set('path', $aData['root']);
			}else
				$html->get('filetp_off', 'sections/control/servers/games/filetp');

					$html->set('id', $id);
					$html->set('server', $sid);

			$html->pack('main');

			$mcache->set('ctrl_server_filetp_'.$sid, $html->arr['main'], false, 10);
		}
	}
?>