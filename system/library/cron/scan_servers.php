<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class scan_servers extends cron
	{
		function __construct()
		{
			global $cfg, $sql, $argv, $start_point, $mcache;

			$servers = $argv;

			unset($servers[0], $servers[1], $servers[2]);

			$sql->query('SELECT `address`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="'.$servers[4].'" LIMIT 1');
			if(!$sql->num())
				return NULL;

			$unit = $sql->get();

			$game = $servers[3];

			unset($servers[3], $servers[4]);

			$sql->query('SELECT `unit` FROM `servers` WHERE `id`="'.$servers[5].'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return NULL;

			foreach($servers as $id)
			{
				$sql->query('SELECT `user`, `uid`, `address`, `status`, `time`, `overdue`, `ftp`, `stop`, `block` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
				$server = $sql->get();

				// Если аренда не закончилась, а сервер просрочен
				if($server['time'] > $start_point && $server['status'] == 'overdue')
				{
					$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

					continue;
				}

				// Если аренда закончилась, а сервер не просрочен (и не заблокирован)
				if($server['time'] < $start_point && !in_array($server['status'], array('overdue', 'blocked')))
				{
					// Убить процессы
					$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
						.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

					if($server['ftp'])
						$ssh->set("mysql -P ".$unit['sql_port']." -u".$unit['sql_login']." -p".$unit['sql_passwd']." --database ".$unit['sql_ftp']." -e \"DELETE FROM ftp WHERE user='".$server['uid']."'\"");

					$sql->query('UPDATE `servers` set `status`="overdue", `online`="0", `players`="", `ftp`="0", `overdue`="'.$start_point.'" WHERE `id`="'.$id.'" LIMIT 1');

					continue;
				}

				// Если аренда закончилась и сервер просрочен длительное время или поставлен на удаление
				if($server['user'] == -1 || ($server['time'] < $start_point && ($server['overdue']+$cfg['server_delete']*86400) < $start_point))
				{
					if($server['user'] != -1)
						$sql->query('UPDATE `servers` set `user`="-1" WHERE `id`="'.$id.'" LIMIT 1');

					exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_delete '.$id.'"');

					continue;
				}

				switch($server['status'])
				{
					case 'working': case 'change': case 'start': case 'restart':
						if(!sys::int($ssh->get('ps aux | grep s_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
						{
							$sql->query('UPDATE `servers` set `status`="off", `online`="0", `players`="0" WHERE `id`="'.$id.'" LIMIT 1');

							// Запуск сервера (если он был выключен не через панель)
							if($server['stop'])
							{
								exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action start '.$game.' '.$id.'"');

								$sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="'.$id.'", `text`="Включение сервера: сервер выключен не через панель", `time`="'.$start_point.'"');
							}
						}else
							exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_scan '.$game.' '.$id.'"');

						break;

					case 'off':
						if(sys::int($ssh->get('ps aux | grep s_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
							$sql->query('UPDATE `servers` set `status`="working" WHERE `id`="'.$id.'" LIMIT 1');
						else{
							// Запуск сервера (если он был выключен не через панель)
							if($server['stop'])
							{
								exec('sh -c "cd /var/enginegp; php cron.php '.$cfg['cron_key'].' server_action start '.$game.' '.$id.'"');

								$sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="'.$id.'", `text`="Включение сервера: сервер выключен не через панель", `time`="'.$start_point.'"');

								continue;
							}
						}

						break;

					case 'reinstall':
						if(!sys::int($ssh->get('ps aux | grep r_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
							$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						break;

					case 'update':
						if(!sys::int($ssh->get('ps aux | grep u_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
							$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						break;

					case 'install':
						if(!sys::int($ssh->get('ps aux | grep i_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
							$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						break;

					case 'recovery':
						if(!sys::int($ssh->get('ps aux | grep rec_'.$server['uid'].' | grep -v grep | awk \'{print $2}\'')))
							$sql->query('UPDATE `servers` set `status`="off" WHERE `id`="'.$id.'" LIMIT 1');

						break;

					case 'blocked':
						if($server['block'] < $start_point)
							$sql->query('UPDATE `servers` set `status`="off", `block`="0" WHERE `id`="'.$id.'" LIMIT 1');
				}
			}

			return NULL;
		}
	}
?>