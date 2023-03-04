<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	include(LIB.'games/actions.php');

	class action extends actions
	{
		public static function start($id, $type = 'start')
		{
			global $cfg, $sql, $user, $start_point;

			$sql->query('SELECT `uid`, `unit`, `tarif`, `game`, `address`, `slots_start`, `name`, `tickrate`, `fps`, `map_start`, `vac`, `time_start`, `core_fix` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
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
				.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

			$taskset = '';

			// Если включена система автораспределения и не установлен фиксированный поток
			if($cfg['cpu_route'] AND !$server['core_fix'])
			{
				$proc_stat = array();

				$proc_stat[0] = $ssh->get('cat /proc/stat');
			}

			// Проверка наличия стартовой карты
			$ssh->set('cd '.$tarif['install'].$server['uid'].'/cstrike/maps/ && ls | grep .bsp | grep -v .bsp.');

			if($server['map_start'] != '' AND !in_array($server['map_start'], str_replace('.bsp', '', explode("\n", $ssh->get()))))
				return array('e' => sys::updtext(sys::text('servers', 'nomap'), array('map' => $server['map_start'].'.bsp')));

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

			// Античит VAC
			$vac = $server['vac'] == 0 ? '-insecure' : '-secure';

			// Боты
			$bots = $cfg['bots'][$server['game']] ? '' : '-nobots';

			// TV
			$tv = $server['tv'] ? '+tv_enable 1 +tv_maxclients 30 +tv_port '.($port+10000) : '-nohltv';

			// FPS
			$fps = $server['fps']+$cfg['fpsplus'];

			// Параметры запуска
			$bash = './srcds_run -debug -game cstrike -norestart -condebug console.log -tickrate '.$server['tickrate'].' +fps_egp '.$fps.' +servercfgfile server.cfg +map \''.$server['map_start'].'\' +maxplayers '.$server['slots_start'].' +ip '.$ip.' +port '.$port.' +sv_lan 0 -nomaster -localcser '.$vac.' '.$bots.' '.$tv;

			// Временный файл
			$temp = sys::temp($bash);

			// Обновление файла start.sh
			$ssh->setfile($temp, $tarif['install'].$server['uid'].'/start.sh', 0500);

			// Строка запуска
			$ssh->set('cd '.$tarif['install'].$server['uid'].';' // переход в директорию игрового сервера
					.'rm *.pid;' // Удаление *.pid файлов
					.'mv console.log oldstart.log;' // Перемещение лога предыдущего запуска в файл oldstart.log
					.'sudo -u server'.$server['uid'].' mkdir -p cstrike/oldstart;' // Создание папки логов
					.'cat cstrike/console.log >> cstrike/oldstart/'.date('d.m.Y_H:i:s', $server['time_start']).'.log; rm cstrike/console.log; rm cstrike/oldstart/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
					.'chown server'.$server['uid'].':1000 start.sh;' // Обновление владельца файла start.sh
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
	}
?>