<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `unit`, `address`, `game`, `status`, `plugins_use`, `ftp_use`, `console_use`, `stats_use`, `copy_use`, `web_use`, `time` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = $sql->get();

	if(!$server['ftp_use'])
		sys::back($cfg['http'].'servers/id/'.$id);

	sys::nav($server, $id, 'filetp');

	$frouter = explode('/', sys::route($server, 'filetp', $go));

	if(end($frouter) == 'noaccess.php')
		include(SEC.'servers/noaccess.php');
	else{
		$sql->query('SELECT `uid`, `unit`, `tarif`, `ftp`, `ftp_root`, `ftp_passwd`, `ftp_on`, `hdd` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
		$server = array_merge($server, $sql->get());

		$sql->query('SELECT `address` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
		$unit = $sql->get();
		$ip = sys::first(explode(':', $unit['address']));

		$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
		$tarif = $sql->get();

		$html->nav($server['address'], $cfg['http'].'servers/id/'.$id);
		$html->nav('FileTP');

		// Корневой каталог сервера
		if($cfg['ftp']['root'][$server['game']] || $server['ftp_root'])
		{
			// Путь для Proftpd
			$homedir = $tarif['install'].$server['uid'];

			// Путь для файлового менеджера
			$dir = $cfg['ftp']['dir'][$server['game']];
		}else{
			// Путь для Proftpd
			$homedir = $tarif['install'].$server['uid'].$cfg['ftp']['home'][$server['game']];

			// Путь для файлового менеджера
			$dir = '/';
		}

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
				$sql->query('SELECT `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
				$unit = array_merge($unit, $sql->get());

				include(LIB.'ssh.php');

				// Проверка соединения с ssh сервером
				if(!$ssh->auth($unit['passwd'], $unit['address']))
					sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');
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
							sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');

						$used = sys::int($ssh->get('cd '.$tarif['install'].$server['uid'].' && du -b | tail -1'));

						if($used < 1)
							sys::back($cfg['http'].'help/action/create');

						$bytes = $server['hdd']*1048576;

						$server['ftp_passwd'] = isset($server['ftp_passwd']{1}) ? $server['ftp_passwd'] : sys::passwd(8);

						$qSql = 'DELETE FROM users WHERE username=\''.$server['uid'].'\';'
								.'DELETE FROM quotalimits WHERE name=\''.$server['uid'].'\';'
								.'DELETE FROM quotatallies WHERE name=\''.$server['uid'].'\';'
								.'INSERT INTO users set username=\''.$server['uid'].'\', password=\''.$server['ftp_passwd'].'\', uid=\''.$server['uid'].'\', gid=\'1000\', homedir=\''.$homedir.'\', shell=\'/bin/false\';'
								.'INSERT INTO quotalimits set name=\''.$server['uid'].'\', quota_type=\'user\', per_session=\'false\', limit_type=\'hard\', bytes_in_avail=\''.$bytes.'\';'
								.'INSERT INTO quotatallies set name=\''.$server['uid'].'\', quota_type=\'user\', bytes_in_used=\''.$used.'\'';

						$ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e "'.$qSql.'"');

						$sql->query('UPDATE `servers` SET `ftp`="1", `ftp_on`="1", `ftp_passwd`="'.$server['ftp_passwd'].'" WHERE `id`="'.$id.'" LIMIT 1');

						$mcache->delete('server_filetp_'.$id);

						sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');

					case 'change':
						if(!$server['ftp'])
							sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');

						$passwd = sys::passwd(8);

						$qSql = "UPDATE users set password='".$passwd."' WHERE username='".$server['uid']."' LIMIT 1";

						$ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e '.'"'.$qSql.'"');

						$sql->query('UPDATE `servers` SET `ftp_passwd`="'.$passwd.'" WHERE `id`="'.$id.'" LIMIT 1');

						$mcache->delete('server_filetp_'.$id);

						sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');

					case 'off':
						if(!$server['ftp'])
							sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');

						$qSql = 'DELETE FROM users WHERE username=\''.$server['uid'].'\';'
								.'DELETE FROM quotalimits WHERE name=\''.$server['uid'].'\';'
								.'DELETE FROM quotatallies WHERE name=\''.$server['uid'].'\'';

						$ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e "'.$qSql.'"');

						$sql->query('UPDATE `servers` SET `ftp`="0" WHERE `id`="'.$id.'" LIMIT 1');

						$mcache->delete('server_filetp_'.$id);

						sys::back($cfg['http'].'servers/id/'.$id.'/section/filetp');

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
						$logs = $mcache->get('filetp_logs_'.$id);

						if(!$logs)
						{
							include(LIB.'ftp.php');

							$ftp = new ftp;

							$logs = $ftp->logs($ssh->get('cat /var/log/proftpd/xferlog | grep "/'.$server['uid'].'/" | awk \'{print $2"\\\"$3"\\\"$4"\\\"$5"\\\"$7"\\\"$8"\\\"$9"\\\"$12}\' | tail -50'), $server['uid']);

							$mcache->set('filetp_logs_'.$id, $logs, false, 300);
						}

						sys::out($logs);
				}

			if(!isset($_POST['path'])) $_POST['path'] = json_encode($aData['root']);

			sys::out($ftp->view($ftp->read(json_decode($_POST['path'])), $id));
		}

		if($mcache->get('server_filetp_'.$id) != '')
			$html->arr['main'] = $mcache->get('server_filetp_'.$id);
		else{
			if($server['ftp'])
			{
				$html->get('filetp_on', 'sections/servers/games/filetp');

					$html->set('address', 'ftp://'.$aData['login'].':'.$aData['passwd'].'@'.$aData['host']);
					$html->set('server', $aData['host']);
					$html->set('login', $aData['login']);
					$html->set('passwd', $aData['passwd']);
					$html->set('path', $aData['root']);
			}else
				$html->get('filetp_off', 'sections/servers/games/filetp');

					$html->set('id', $id);

			$html->pack('main');

			$mcache->set('server_filetp_'.$id, $html->arr['main'], false, 10);
		}
	}
?>