<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    include(LIB.'games/actions.php');

	class action extends actions
    {
        public static function start($id, $type = 'start')
        {
			global $cfg, $sql, $user, $start_point;

			$sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `slots_start`, `name`, `ram`, `time_start` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
            $tarif = $sql->get();

			include(LIB.'ssh.php');

			$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return array('e' => sys::text('error', 'ssh'));

			list($ip, $port) = explode(':', $server['address']);

			// Убить процессы
			$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
				.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe;');

			// Временный файл
			$temp = sys::temp(action::config($ip, $port, $server['slots_start'], $ssh->get('cat '.$tarif['install'].'/'.$server['uid'].'/server.properties')));

			// Обновление файла server.cfg
			$ssh->setfile($temp, $tarif['install'].$server['uid'].'/server.properties', 0644);

			unlink($temp);

			// Параметры запуска
			$bash = 'java -Xmx'.$server['ram'].'M -Xms'.$server['ram'].'M -jar start.jar nogui';

			// Временный файл
			$temp = sys::temp($bash);

			// Обновление файла start.sh
			$ssh->setfile($temp, $tarif['install'].$server['uid'].'/start.sh', 0500);

			// Строка запуска
			$ssh->set('cd '.$tarif['install'].$server['uid'].';' // переход в директорию игрового сервера
					.'sudo -u server'.$server['uid'].' mkdir -p oldstart;' // Создание папки логов
					.'cat console.log >> oldstart/'.date('d.m.Y_H:i:s', $server['time_start']).'.log; rm console.log; rm oldstart/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
					.'chown server'.$server['uid'].':1000 server.properties start.sh;' // Обновление владельца файлов
					.'sudo -u server'.$server['uid'].' screen -dmS s_'.$server['uid'].' '.$taskset.' sh -c "./start.sh > console.log"'); // Запуск игровго сервера

			// Обновление информации в базе
			$sql->query('UPDATE `servers` set `status`="'.$type.'", `online`="0", `players`="", `time_start`="'.$start_point.'", `stop`="1" WHERE `id`="'.$id.'" LIMIT 1');

			unlink($temp);

			// Сброс кеша
			actions::clmcache($id);

			sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0, 'players' => ''));
			sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0));

			return array('s' => 'ok');
		}

		public static function config($ip, $port, $slots, $config)
		{
			$aLine = explode("\n", $config);

			$search = array(
				"#^server-ip=(.*?)$#is",
				"#^server-port=(.*?)$#is",
				"#^rcon\.port=(.*?)$#is",
				"#^query\.port=(.*?)$#is",
				"#^max-players=(.*?)$#is",
				"#^enable-query=(.*?)$#is",
				"#^debug=(.*?)$#is"
			);

			$config = '';

			foreach($aLine as $line)
			{
				if(str_replace(array(' ', "\t"), '', $line) != '')
					$edit = trim(preg_replace($search, array('','','','','','',''), $line));

				if($edit != '')
					$config .= $edit.PHP_EOL;

				$edit = '';
			}

			$config .= 'server-ip='.$ip.PHP_EOL
					.'server-port='.$port.PHP_EOL
					.'rcon.port='.($port+10000).PHP_EOL
					.'query.port='.($port).PHP_EOL
					.'max-players='.$slots.PHP_EOL
					.'enable-query=true'.PHP_EOL
					.'debug=false';

			return $config;
		}
    }
?>