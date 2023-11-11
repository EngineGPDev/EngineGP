<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class actions
	{
		public static function stop($id)
		{
			global $cfg, $sql, $user;

			include(LIB.'ssh.php');

			$sql->query('SELECT `uid`, `unit`, `game`, `address`, `name` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return array('e' => sys::text('error', 'ssh'));

			$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
					.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

			// Обновление информации в базе
			$sql->query('UPDATE `control_servers` set `status`="off", `online`="0", `players`="", `stop`="0" WHERE `id`="'.$id.'" LIMIT 1');

			// Сброс кеша
			actions::clmcache($id);

			sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0, 'players' => ''));
			sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'off', 'online' => 0));

			return array('s' => 'ok');
		}

		public static function change($id, $map = false)
		{
			global $cfg, $sql, $html, $user, $mcache;

			// Если в кеше есть карты
			if($mcache->get('ctrl_server_maps_change_'.$id) != '' && !$map)
				return array('maps' => $mcache->get('ctrl_server_maps_change_'.$id));

			include(LIB.'ssh.php');

			$sql->query('SELECT `uid`, `unit`, `game`, `online`, `players`, `name` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return array('e' => sys::text('error', 'ssh'));

			// Массив карт игрового сервера (папка "maps")
			$aMaps = explode("\n", $ssh->get('cd /servers/'.$server['uid'].'/cstrike/maps/ && ls | grep .bsp | grep -v .bsp.'));

			// Удаление пустого элемента
			unset($aMaps[count($aMaps)-1]);

			// Удаление ".bsp"
			$aMaps = str_replace('.bsp', '', $aMaps);

			// Если выбрана карта
			if($map)
			{
				// Проверка наличия выбранной карты
				if(!in_array($map, $aMaps))
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
				$html->get('change_list', 'sections/control/servers/games');
					$html->set('img', file_exists(DIR.'/maps/'.$server['game'].'/'.$map.'.jpg') ? $cfg['http'].'maps/'.$server['game'].'/'.$map.'.jpg' : $cfg['http'].'template/images/status/none.jpg');
					$html->set('name', $map);
					$html->set('id', $server['unit']);
					$html->set('server', $id);
				$html->pack('maps');
			}

			// Запись карт в кеш
			$mcache->set('ctrl_server_maps_change_'.$id, $html->arr['maps'], false, 30);

			return array('maps' => $html->arr['maps']);
		}

		public static function reinstall($id)
		{
			global $cfg, $sql, $user, $start_point;

			include(LIB.'ssh.php');

			$sql->query('SELECT `uid`, `unit`, `address`, `game`, `name`, `pack`, `ftp`, `core_fix` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd`, `fcpu` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return array('e' => sys::text('error', 'ssh'));

			$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
				.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

			$taskset = '';

			// Если включена система автораспределения и не установлен фиксированный поток
			if(!$server['core_fix'])
			{
				$proc_stat = array();

				$proc_stat[0] = $ssh->get('cat /proc/stat');
			}

			$path = 'rm '.$server['pack'].'.zip; wget '.$cfg['control_server'].'/'.$server['pack'].'.zip; unzip '.$server['pack'].'.zip; rm '.$server['pack'].'.zip;';

			if(in_array($server['game'], array('css', 'csgo')))
				$path = 'cd '.$cfg['steamcmd'].'; ./steamcmd.sh +login anonymous +force_install_dir "/servers/'.$uid.'" +app_update '.$cfg['control_steamcmd'][$game].' +quit;';

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

			$ssh->set('rm -r '.$install.';' // Удаление директории игрового сервера
				.'mkdir '.$install.';' // Создание директории
				.'chown server'.$server['uid'].':1000 '.$install.';' // Изменение владельца и группы директории
				.'cd '.$install.' && sudo -u server'.$server['uid'].' '.$taskset.' screen -dmS r_'.$server['uid'].' sh -c "'
				.$path // Копирование файлов сборки для сервера
				.'find . -type d -exec chmod 700 {} \;;'
				.'find . -type f -exec chmod 600 {} \;;'
				.'chmod 500 '.params::$aFileGame[$server['game']].'"');

			// Очистка записей в базе
			$sql->query('DELETE FROM `control_admins_'.$server['game'].'` WHERE `server`="'.$id.'"'); // Список админов на сервере
			$sql->query('DELETE FROM `control_plugins_install` WHERE `server`="'.$id.'"'); // Список установленных плагинов на сервере

			$core = !isset($core) ? 0 : $core+1;

			// Обновление информации в базе
			$sql->query('UPDATE `control_servers` set `status`="reinstall", `core_use`="'.$core.'", `fastdl`="0" WHERE `id`="'.$id.'" LIMIT 1');

			// Сброс кеша
			actions::clmcache($id);

			sys::reset_mcache('ctrl_server_scan_mon_pl_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'reinstall', 'online' => 0, 'players' => ''));
			sys::reset_mcache('ctrl_server_scan_mon_'.$id, $id, array('name' => $server['name'], 'game' => $server['game'], 'status' => 'reinstall', 'online' => 0));

			return array('s' => 'ok');
		}

		public static function update($id)
		{
			global $cfg, $sql, $user, $start_point;

			include(LIB.'ssh.php');

			$sql->query('SELECT `uid`, `unit`, `address`, `game`, `name`, `pack`, `ftp` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			$sql->query('SELECT `address`, `passwd`, `fcpu` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return array('e' => sys::text('error', 'ssh'));

			$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
				.'lsof -i@'.$server['address'].' | awk '."'{print $2}'".' | grep -v PID | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

			$taskset = '';

			// Если включена система автораспределения и не установлен фиксированный поток
			if(!$server['core_fix'])
			{
				$proc_stat = array();

				$proc_stat[0] = $ssh->get('cat /proc/stat');
			}

			$path = 'rm '.$server['pack'].'_udp.zip; wget '.$cfg['control_server'].'/'.$server['pack'].'_udp.zip; unzip -u '.$server['pack'].'_udp.zip; rm '.$server['pack'].'_udp.zip;';

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

			$ssh->set('cd '.$install.' && sudo -u server'.$server['uid'].' '.$taskset.' screen -dmS u_'.$server['uid'].' sh -c "'.$path // Копирование файлов обвновления сборки для сервера
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

		public static function delete($id)
		{
			global $cfg, $sql, $user;

			include(LIB.'ssh.php');

			$sql->query('SELECT `uid`, `unit`, `game`, `slots`, `address` FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');
			$server = $sql->get();

			if(!$server['uid'])
				return array('e' => 'uid 404');

			$sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `control` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соедниения пу с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return array('e' => sys::text('error', 'ssh'));

			// Убить процессы
			$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
				.'lsof -i@:'.$server['address'].' | awk '."'{print $2}'".' | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

			// Директория игрового сервера
			$install = '/servers/'.$server['uid'];

			$copys = 'screen -dmS r_copy_'.$server['uid'].' sh -c "';

			$scopy = $sql->query('SELECT `id`, `name` FROM `control_copy` WHERE `server`="'.$id.'"');
			while($copy = $sql->get($scopy))
			{
				$copys .= 'rm /copy/'.$copy['name'].'.tar;';

				$sql->query('DELETE FROM `control_copy` WHERE `id`="'.$copy['id'].'" LIMIT 1');
			}

			$copys .= '";';

			$ssh->set($copys // Удаление резервных копий
					.'screen -dmS r_'.$server['uid'].' sh -c "rm -r '.$install.';' // Удаление директории сервера
					.'userdel server'.$server['uid'].'"'); // Удаление пользователя сервера c локации

			// Удаление ftp доступа
			$qSql = 'DELETE FROM users WHERE username=\''.$server['uid'].'\';';

            $ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e "'.$qSql.'"');

			// Очистка правил FireWall
			ctrl::iptables($id, 'remove', NULL, NULL, $server['unit'], false, $ssh);

			// Удаление заданий из crontab
			$sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
			$panel = $sql->get();

			if(!$ssh->auth($panel['passwd'], $panel['address']))
				return array('e' => 'Неудалось создать связь с панелью');

			$crons = $sql->query('SELECT `id`, `cron` FROM `control_crontab` WHERE `server`="'.$id.'"');
			while($cron = $sql->get($crons))
			{
				$ssh->set('echo "" >> /etc/crontab && cat /etc/crontab');
				$crontab = str_replace($cron['cron'], '', $ssh->get());

				// Временный файл
				$temp = sys::temp($crontab);

				$ssh->setfile($temp, '/etc/crontab', 0644);

				$ssh->set("sed -i '/^$/d' /etc/crontab");
				$ssh->set('crontab -u root /etc/crontab');

				unlink($temp);

				$sql->query('DELETE FROM `control_crontab` WHERE `id`="'.$cron['id'].'" LIMIT 1');
			}

			// Удаление различной информации игрового сервера
			$sql->query('DELETE FROM `control_admins_'.$server['game'].'` WHERE `server`="'.$id.'" LIMIT 1');
			$sql->query('DELETE FROM `control_plugins_install` WHERE `server`="'.$id.'" LIMIT 1');
			$sql->query('DELETE FROM `control_plugins_buy` WHERE `server`="'.$id.'" LIMIT 1');
			$sql->query('DELETE FROM `control_servers` WHERE `id`="'.$id.'" LIMIT 1');

			return array('s' => 'ok');
		}

		public static function clmcache($id)
		{
			global $mcache;

			$mcache->delete('ctrl_server_index_'.$id);
			$mcache->delete('ctrl_server_resources_'.$id);
			$mcache->delete('ctrl_server_status_'.$id);
		}
	}