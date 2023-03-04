<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    include(LIB.'games/actions.php');

	class action extends actions
    {
        public static function start($id, $type = 'start')
        {
			global $cfg, $sql, $user, $start_point;

			$sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `slots_start`, `name`, `time_start`, `core_fix` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
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
			$temp = sys::temp(action::config($ip, $port, $server['slots_start'], $ssh->get('cat '.$tarif['install'].'/'.$server['uid'].'/mods/deathmatch/mtaserver.conf')));

			// Обновление файла server.cfg
			$ssh->setfile($temp, $tarif['install'].$server['uid'].'/mods/deathmatch/mtaserver.conf', 0644);

			unlink($temp);

			$taskset = '';

			// Если включена система автораспределения и не установлен фиксированный поток
			if($cfg['cpu_route'] AND !$server['core_fix'])
			{
				$proc_stat = array();

				$proc_stat[0] = $ssh->get('cat /proc/stat');
			}

			// Если система автораспределения продолжить парсинг загрузки процессора
			if(isset($proc_stat))
			{
				$proc_stat[1] = $ssh->get('cat /proc/stat');

				// Ядро/поток, на котором будет запущен игровой сервер (поток выбран с рассчетом наименьшей загруженности в момент запуска игрового сервера)
				$core = sys::cpu_idle($proc_stat, $server['unit'], false); // число от 1 до n (где n число ядер/потоков в процессоре (без нулевого)

				if(!is_numeric($core))
					return array('e' => sys::text('error', 'cpu'));

				$taskset = 'taskset -c '.$core;
			}

			if($server['core_fix'])
			{
				$core = $server['core_fix']-1;
				$taskset = 'taskset -c '.$core;
			}

			// Параметры запуска
			$bash = './mta-server';

			// Временный файл
			$temp = sys::temp($bash);

			// Обновление файла start.sh
			$ssh->setfile($temp, $tarif['install'].$server['uid'].'/start.sh', 0500);

			// Строка запуска
			$ssh->set('cd '.$tarif['install'].$server['uid'].';' // переход в директорию игрового сервера
					.'rm *.pid;' // Удаление *.pid файлов
					.'sudo -u server'.$server['uid'].' mkdir -p mods/deathmatch/oldstart;' // Создание папки логов
					.'cat mods/deathmatch/logs/server.log >> mods/deathmatch/oldstart/'.date('d.m.Y_H:i:s', $server['time_start']).'.log; rm mods/deathmatch/logs/server.log; rm mods/deathmatch/logs/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
					.'chown server'.$server['uid'].':1000 mods/deathmatch/mtaserver.conf start.sh;' // Обновление владельца файлов
					.'sudo -u server'.$server['uid'].' screen -dmS s_'.$server['uid'].' '.$taskset.' sh -c "./start.sh"'); // Запуск игровго сервера

			$core = !isset($core) ? 0 : $core+1;

			// Обновление информации в базе
			$sql->query('UPDATE `servers` set `status`="'.$type.'", `online`="0", `players`="", `core_use`="'.$core.'", `time_start`="'.$start_point.'", `stop`="1" WHERE `id`="'.$id.'" LIMIT 1');

			unlink($temp);

			// Сброс кеша
			actions::clmcache($id);

			sys::reset_mcache('server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0, 'players' => ''));
			sys::reset_mcache('server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0));

			return array('s' => 'ok');
		}

		public static function config($ip, $port, $slots, $config)
		{
			$aRepl = array(
				'<serverip>', '</serverip>',
				'<maxplayers>', '</maxplayers>',
				'<httpserver>', '</httpserver>',
				'<serverport>', '</serverport>',
				'<httpport>', '</httpport>',
				'<logfile>', '</logfile>',
				'<ase>', '</ase>'
			);

			$config = str_ireplace($aRepl, $aRepl, $config);
			$config = str_ireplace(array('<config>', '</config>', "\r", "\t"),'', $config);
			$config = preg_replace("#\<\!--(.+?)--\>#is", '', $config);

			$aLine = explode("\n", $config);

			$search = array(
				"#\<serverip\>(.+?)\<\/serverip\>#is",
				"#\<maxplayers\>(.+?)\<\/maxplayers\>#is",
				"#\<httpserver\>(.+?)\<\/httpserver\>#is",
				"#\<serverport\>(.+?)\<\/serverport\>#is",
				"#\<httpport\>(.+?)\<\/httpport\>#is",
				"#\<logfile\>(.+?)\<\/logfile\>#is",
				"#\<ase\>(.+?)\<\/ase\>#is"
			);

			$config = '<config>'.PHP_EOL;

			foreach($aLine as $line)
			{
				if(str_replace(array(' ', "\t"), '', $line) != '')
					$edit = trim(preg_replace($search, array('','','','','',''), $line));

				if($edit != '')
					$config .= '    '.$edit.PHP_EOL;

				$edit = '';
			}

			$config .= '    <serverip>'.$ip.'</serverip>'.PHP_EOL
					.'    <maxplayers>'.$slots.'</maxplayers>'.PHP_EOL
					.'    <httpserver>1</httpserver>'.PHP_EOL
					.'    <serverport>'.$port.'</serverport>'.PHP_EOL
					.'    <httpport>'.($port+10000).'</httpport>'.PHP_EOL
					.'    <ase>1</ase>'.PHP_EOL
					.'    <logfile>logs/server.log</logfile>';

			return $config."\n".'</config>';
		}
    }
?>