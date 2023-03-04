<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    include(LIB.'control/actions.php');

	class action extends actions
    {
        public static function start($id, $type = 'start')
        {
			global $cfg, $sql, $user, $start_point;

			$sql->query('SELECT `uid`, `unit`, `game`, `address`, `slots`, `name`, `tickrate`, `map_start`, `vac`, `time_start`, `core_fix`, `pingboost` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			include(LIB.'ssh.php');

			$sql->query('SELECT `address`, `passwd`, `fcpu` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
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
			if(!$server['core_fix'])
			{
				$proc_stat = array();

				$proc_stat[0] = $ssh->get('cat /proc/stat');
			}

			// Проверка наличия стартовой карты
			$ssh->set('cd /servers/'.$server['uid'].'/csgo/maps/ && du -ah | grep -e "\.bsp$" | awk \'{print $2}\'');

			include_once(LIB.'games/games.php');

			if(games::map($server['map_start'], $ssh->get()))
				return array('e' => sys::updtext(sys::text('servers', 'nomap'), array('map' => $server['map_start'].'.bsp')));

			// Если система автораспределения продолжить парсинг загрузки процессора
			if(isset($proc_stat))
			{
				$proc_stat[1] = $ssh->get('cat /proc/stat');

				// Ядро/поток, на котором будет запущен игровой сервер (поток выбран с рассчетом наименьшей загруженности в момент запуска игрового сервера)
				$core = sys::cpu_idle($proc_stat, $server['unit'], $unit['fcpu'], true); // число от 1 до n (где n число ядер/потоков в процессоре (без нулевого)

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

			// TV
			$tv = $server['tv'] ? '+tv_enable 1 +tv_maxclients 30 +tv_port '.($port+10000) : '-nohltv';

			$check = explode('/', $server['map_start']);

			// Стартовая карта
			$map = $check[0] == 'workshop' ? '+workshop_start_map '.$check[1] : '+map \''.$server['map_start'].'\'';

			// Игровой режим
			$mods = array(
				1 => '+game_type 0 +game_mode 0',
				2 => '+game_type 0 +game_mode 1',
				3 => '+game_type 1 +game_mode 0',
				4 => '+game_type 1 +game_mode 1',
				5 => '+game_type 1 +game_mode 2'
			);

			$mod = !$server['pingboost'] ? $mods[2] : $mods[$server['pingboost']];

			// Параметры запуска
			$bash = './srcds_run -debug -game csgo -norestart -condebug console.log -usercon -tickrate '.$server['tickrate'].' '.$mod.' +servercfgfile server.cfg '.$map.' -maxplayers_override '.$server['slots'].' +ip '.$ip.' +net_public_adr '.$ip.' +port '.$port.' -sv_lan 0 '.$vac.' '.$tv;

			// Временный файл
			$temp = sys::temp($bash);

			// Обновление файла start.sh
			$ssh->setfile($temp, '/servers/'.$server['uid'].'/start.sh', 0500);

			// Строка запуска
			$ssh->set('cd /servers/'.$server['uid'].';' // переход в директорию игрового сервера
					.'rm *.pid;' // Удаление *.pid файлов
					.'sudo -u server'.$server['uid'].' mkdir -p csgo/oldstart;' // Создание папки логов
					.'cat csgo/console.log >> csgo/oldstart/'.date('d.m.Y_H:i:s', $server['time_start']).'.log; rm csgo/console.log; rm csgo/oldstart/01.01.1970_03:00:00.log;'  // Перемещение лога предыдущего запуска
					.'chown server'.$server['uid'].':1000 start.sh;' // Обновление владельца файла start.sh
					.'sudo -u server'.$server['uid'].' screen -dmS s_'.$server['uid'].' '.$taskset.' sh -c "./start.sh"'); // Запуск игровго сервера

			$core = !isset($core) ? 0 : $core+1;

			// Обновление информации в базе
			$sql->query('UPDATE `control_servers` set `status`="'.$type.'", `online`="0", `players`="", `core_use`="'.$core.'", `time_start`="'.$start_point.'", `stop`="1" WHERE `id`="'.$id.'" LIMIT 1');

			unlink($temp);

			// Сброс кеша
			actions::clmcache($id);

			sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0, 'players' => ''));
			sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => $type, 'online' => 0));

			return array('s' => 'ok');
		}

        public static function change($id, $map = false)
        {
			global $cfg, $sql, $html, $user, $mcache;

			// Если в кеше есть карты
			if($mcache->get('ctrl_server_maps_change_'.$id) != '' AND !$map)
				return array('maps' => $mcache->get('ctrl_server_maps_change_'.$id));

			include(LIB.'ssh.php');

			include(LIB.'games/games.php');

			$sql->query('SELECT `uid`, `unit`, `game`, `online`, `players`, `name` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
            $unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
            if(!$ssh->auth($unit['passwd'], $unit['address']))
                return array('e' => sys::text('error', 'ssh'));

			// Массив карт игрового сервера (папка "maps")
			$aMaps = explode("\n", $ssh->get('cd /servers/'.$server['uid'].'/csgo/maps/ && du -ah | grep -e "\.bsp$" | awk \'{print $2}\''));

			// Удаление пустого элемента
			unset($aMaps[count($aMaps)-1]);

			// Удаление ".bsp"
			$aMaps = str_ireplace(array('./', '.bsp'), '', $aMaps);

			// Если выбрана карта
			if($map)
			{
				$map = str_replace('|', '/', $map);

				// Проверка наличия выбранной карты
				if(games::map($map, $aMaps))
					return array('e' => sys::updtext(sys::text('servers', 'change'), array('map' => $map.'.bsp')));

				// Отправка команды changelevel
				$ssh->set('sudo -u server'.$server['uid'].' screen -p 0 -S s_'.$server['uid'].' -X eval '."'stuff \"changelevel ".sys::cmd($map)."\"\015'");

				// Обновление информации в базе
				$sql->query('UPDATE `control_servers` set `status`="change" WHERE `id`="'.$id.'" LIMIT 1');

				// Сброс кеша
				actions::clmcache($id);

				sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'change', 'online' => $server['online'], 'players' => base64_decode($server['players'])));
				sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'change', 'online' => $server['online']));

				return array('s' => 'ok');
			}

			// Сортировка списка карт
			sort($aMaps);
			reset($aMaps);

			// Генерация списка карт для выбора
			foreach($aMaps as $map)
			{
				$aName = explode('/', $map);
				$name = end($aName);

				$html->get('change_list', 'sections/control/servers/csgo');

					$html->set('img', file_exists(DIR.'/maps/'.$server['game'].'/'.$name.'.jpg') ? $cfg['http'].'maps/'.$server['game'].'/'.$name.'.jpg' : $cfg['http'].'template/images/status/none.jpg');
					$html->set('map', str_replace('/', '|', $map));
					$html->set('name', $name);
					$html->set('id', $server['unit']);
					$html->set('server', $id);

					if(count($aName) > 1)
						$html->unit('workshop', true);
					else
						$html->unit('workshop');

				$html->pack('maps');
			}

			// Запись карт в кеш
			$mcache->set('ctrl_server_maps_change_'.$id, $html->arr['maps'], false, 60);

			return array('maps' => $html->arr['maps']);
		}

        public static function update($id)
        {
			global $cfg, $sql, $user, $start_point;

			include(LIB.'ssh.php');

			$sql->query('SELECT `uid`, `unit`, `game`, `name`, `ftp`, `core_fix` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp`, `fcpu` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
            $unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
            if(!$ssh->auth($unit['passwd'], $unit['address']))
                return array('e' => sys::text('error', 'ssh'));

			$taskset = '';

			// Если включена система автораспределения и не установлен фиксированный поток
			if(!$server['core_fix'])
			{
				$proc_stat = array();

				$proc_stat[0] = $ssh->get('cat /proc/stat');
			}

			// Директория игрового сервера
			$install = '/servers/'.$server['uid'];

			// Если система автораспределения продолжить парсинг загрузки процессора
			if(isset($proc_stat))
			{
				$proc_stat[1] = $ssh->get('cat /proc/stat');

				// Ядро/поток, на котором будет запущен игровой сервер (поток выбран с рассчетом наименьшей загруженности в момент запуска игрового сервера)
				$core = sys::cpu_idle($proc_stat, $server['unit'], $unit['fcpu'], true); // число от 1 до n (где n число ядер/потоков в процессоре (без нулевого)

				if(!is_numeric($core))
					return array('e' => 'Не удается выполнить операцию, нет свободного потока.');

				$taskset = 'taskset -c '.$core;
			}

			if($server['core_fix'])
			{
				$core = $server['core_fix']-1;
				$taskset = 'taskset -c '.$core;
			}

			$ssh->set('cd '.$cfg['steamcmd'].' && '.$taskset.' screen -dmS u_'.$server['uid'].' sh -c "'
				.'./steamcmd.sh +login anonymous +force_install_dir "'.$install.'" +app_update 740 +quit;'
				.'cd '.$install.';'
				.'chown -R server'.$server['uid'].':servers .;'
				.'find . -type d -exec chmod 700 {} \;;'
				.'find . -type f -exec chmod 600 {} \;;'
				.'chmod 500 '.params::$aFileGame[$server['game']].'"');

			$core = !isset($core) ? 0 : $core+1;

			// Обновление информации в базе
			$sql->query('UPDATE `control_servers` set `status`="update", `core_use`="'.$core.'" WHERE `id`="'.$id.'" LIMIT 1');

			// Сброс кеша
			actions::clmcache($id);

			sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'update', 'online' => 0, 'players' => ''));
			sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'update', 'online' => 0));

			return array('s' => 'ok');
		}
    }
?>