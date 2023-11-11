<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    class service
    {
        public static function buy($aData = array())
        {
			global $cfg, $sql, $user, $start_point;

			// Проверка локации
			$sql->query('SELECT `address`, `test` FROM `units` WHERE `id`="'.$aData['unit'].'" AND `mta`="1" AND `show`="1" LIMIT 1');
			if(!$sql->num())
				sys::outjs(array('e' => 'Локация не найдена.'));

			$unit = $sql->get();

			// Проверка тарифа
			$sql->query('SELECT `id` FROM `tarifs` WHERE `id`="'.$aData['tarif'].'" AND `unit`="'.$aData['unit'].'" AND `show`="1" LIMIT 1');
			if(!$sql->num())
				sys::outjs(array('e' => 'Тариф не найден.'));

			$sql->query('SELECT '
				.'`slots_min`,'
				.'`slots_max`,'
				.'`port_min`,'
				.'`port_max`,'
				.'`hostname`,'
				.'`packs`,'
				.'`time`,'
				.'`test`,'
				.'`tests`,'
				.'`discount`,'
				.'`ftp`,'
				.'`plugins`,'
				.'`console`,'
				.'`stats`,'
				.'`copy`,'
				.'`web`,'
				.'`plugins_install`,'
				.'`hdd`,'
				.'`autostop`,'
				.'`core_fix`,'
				.'`ip`,'
				.'`price`'
				.' FROM `tarifs` WHERE `id`="'.$aData['tarif'].'" LIMIT 1');

			$tarif = $sql->get();

			// Проверка сборки
			if(!array_key_exists($aData['pack'], sys::b64djs($tarif['packs'], true)))
				sys::outjs(array('e' => 'Сборка не найдена.'));

			$test = 0;

			// Проверка периода на тест
			if($aData['test'])
			{
				if(!$tarif['test'] || !$unit['test'])
					sys::outjs(array('e' => 'Тестовый период недоступен.'));


				// Проверка на повторный запрос
				$sql->query('SELECT `id`, `game` FROM `tests` WHERE `user`="'.$user['id'].'" LIMIT 1');
				if($sql->num())
				{
					$test_info = $sql->get();

					if(!$cfg['tests']['game'] || $test_info['game'] == 'mta')
						sys::outjs(array('e' => 'Тестовый период предоставляется один раз.'));

					$sql->query('SELECT `id` FROM `servers` WHERE `user`="'.$user['id'].'" AND `test`="1" LIMIT 1');
					if($sql->num() AND !$cfg['tests']['sametime'])
						sys::outjs(array('e' => 'Чтобы получить тестовый период другой игры, дождитесь окончания текущего.'));
				}

				// Проверка наличия мест на локации
				$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$aData['unit'].'" AND `test`="1" AND `time`>"'.$start_point.'" LIMIT '.$unit['test']);
				if($sql->num() == $unit['test'])
					sys::outjs(array('e' => 'Свободного места для тестового периода нет.'));

				// Проверка наличия мест для выбранного тарифа
				$sql->query('SELECT `id` FROM `servers` WHERE `tarif`="'.$aData['tarif'].'" AND `test`="1" AND `time`>"'.$start_point.'" LIMIT '.$tarif['tests']);
				if($sql->num() == $tarif['tests'])
					sys::outjs(array('e' => 'Свободного места для тестового периода выбранного тарифа нет.'));

				$test = 1;
			}else
				// Проверка периода
				if(!$cfg['settlement_period'] AND !in_array($aData['time'], explode(':', $tarif['time'])))
					sys::outjs(array('e' => 'Переданные данные периода неверны.'));

			// Проверка слот
			if($aData['slots'] < $tarif['slots_min'] || $aData['slots'] > $tarif['slots_max'])
				sys::outjs(array('e' => 'Переданные данные слот неверны.'));

			// Определение суммы
			if($cfg['settlement_period'])
			{
				// Цена аренды за расчетный период
				$sum = games::define_sum($tarif['discount'], $tarif['price'], $aData['slots'], $start_point);

				$aData['time'] = games::define_period('buy', params::$aDayMonth);
			}else
				$sum = games::define_sum($tarif['discount'], $tarif['price'], $aData['slots'], $aData['time']);

			// Проверка промо-кода
			$promo = games::define_promo(
				$aData['promo'],
				array(
					'tarif' => $aData['tarif'],
					'slots' => $aData['slots'],
					'time' => $aData['time'],
					'user' => $user['id']
				),
				$tarif['discount'],
				$sum
			);

			$days = $aData['time']; // Кол-во дней аренды

			// Использование промо-кода
			if(is_array($promo))
			{
				if(array_key_exists('sum', $promo))
					$sum = $promo['sum'];
				else
					$days += $promo['days']; // Кол-во дней аренды с учетом подарочных (промо-код)
			}

			// Проверка баланса
			if($user['balance'] < $sum)
				sys::outjs(array('e' => 'У вас не хватает '.(round($sum-$user['balance'], 2)).' '.$cfg['currency']));

			// Выделенный адрес игрового сервера
			if(!empty($tarif['ip']))
			{
				$aIp = explode(':', $tarif['ip']);

				$ip = false;
				$port = params::$aDefPort['mta'];

				// Проверка наличия свободного адреса
				foreach($aIp as $adr)
				{
					$adr = trim($adr);

					$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$aData['unit'].'" AND `address` LIKE "'.$adr.':%" LIMIT 1');
					if(!$sql->num())
					{
						$ip = $adr;

						break;
					}
				}
			}else{
				$ip = sys::first(explode(':', $unit['address']));
				$port = false;

				// Проверка наличия свободного порта
				for($tarif['port_min']; $tarif['port_min'] <= $tarif['port_max']; $tarif['port_min']+=1)
				{
					$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$aData['unit'].'" AND (`address`="'.$ip.':'.$tarif['port_min'].'" OR `port`="'.$tarif['port_min'].'") LIMIT 1');
					if(!$sql->num())
					{
						$port = $tarif['port_min'];

						break;
					}
				}
			}

			if(!$ip || !$port)
			{
				$sql->query('UPDATE `tarifs` set `show`="0" WHERE `id`="'.$aData['tarif'].'" LIMIT 1');

				sys::outjs(array('e' => 'К сожалению нет доступных мест, обратитесь в тех.поддержку.'));
			}

			if($test)
				$aData['time'] = games::time($start_point, $tarif['test']);
			else
				$aData['time'] = games::time($start_point, $days);

			$fix_one = 0;
			$core = 0;

			if($tarif['core_fix'] != '')
			{
				$aCore = explode(',', $tarif['core_fix']);

				foreach($aCore as $cpu)
				{
					$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$aData['unit'].'" AND `tarif`="'.$aData['tarif'].'" AND `core_fix`="'.$cpu.'" AND `core_fix_one`="1" LIMIT 1');

					if($sql->num())
						continue;

					$fix_one = 1;
					$core = $cpu;

					break;
				}

				if(!$core)
				{
					$sql->query('UPDATE `tarifs` set `show`="0" WHERE `id`="'.$aData['tarif'].'" LIMIT 1');

					sys::outjs(array('e' => 'К сожалению нет доступных мест, обратитесь в тех.поддержку.'));
				}
			}

			$ram = $tarif['param_fix'] ? $aData['ram'] : $cfg['ram']['mta']*$aSDATA['slots'];

			// Массив данных
			$aSDATA = array(
				'unit' => $aData['unit'], // идентификатор локации
				'tarif' => $aData['tarif'], // идентификатор тарифа
				'ram' => $ram, // значение ram
				'param_fix' => $tarif['param_fix'], // фиксированные параметры
				'pack' => $aData['pack'], // Выбранная сборка для установки
				'time' => $aData['time'], // Время аренды
				'days' => $days, // Число дней
				'sum' => $sum, // Сумма списания
				'test' => $test, // тестовый период
				'address' => $ip.':'.$port, // адрес игрового сервера
				'port' => $port, // порт игрового сервера
				'slots' => $aData['slots'], // Кол-во слот
				'autostop' => $tarif['autostop'], // Выключение при 0 онлайне
				'ftp' => $tarif['ftp'], // Использование ftp
				'plugins' => $tarif['plugins'], // Использование плагинов
				'console' => $tarif['console'], // Использование консоли
				'stats' => $tarif['stats'], // Использование графиков (ведение статистики)
				'copy' => $tarif['copy'], // Использование резервных копий
				'web' => $tarif['web'], // Использование доп услуг
				'plugins_install' => $tarif['plugins_install'], // Список установленных плагинов
				'hdd' => $tarif['hdd'], // Дисковое пространство
				'core_fix' => $core, // Выделенный поток
				'core_fix_one' => $fix_one, // Выделенный поток
				'promo' => $promo // Использование промо-кода
			);

			return $aSDATA;
		}

		public static function install($aSDATA = array())
		{
			global $cfg, $sql, $user, $start_point;

			include(LIB.'ssh.php');

			// Массив данных локации (адрес,пароль)
			$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$aSDATA['unit'].'" LIMIT 1');
			$unit = $sql->get();

			// Проверка ssh соединения с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				sys::outjs(array('e' => sys::text('error', 'ssh')));

			// Массив данных тарифа (путь сборки,путь установки)
			$sql->query('SELECT `path`, `install`, `hostname` FROM `tarifs` WHERE `id`="'.$aSDATA['tarif'].'" LIMIT 1');
			$tarif = $sql->get();

			// Получение идентификаторов игрового сервера
			$sql->query('INSERT INTO `servers` set uid="1"');
			$id = $sql->id();
			$uid = $id+1000;

			// Директория сборки
			$path = $tarif['path'].$aSDATA['pack'];

			// Директория игрового сервера
			$install = $tarif['install'].$uid;

			$ssh->set('mkdir '.$install.';' // Создание директории
					.'useradd -s /bin/false -d '.$install.' -g servers -u '.$uid.' server'.$uid.';' // Создание пользователя сервера на локации
					.'chown server'.$uid.':1000 '.$install.';' // Изменение владельца и группы директории
					.'cd '.$install.' && sudo -u server'.$uid.' screen -dmS i_'.$uid.' sh -c "cp -r '.$path.'/. .;' // Копирование файлов сборки для сервера
					.'find . -type d -exec chmod 700 {} \;;'
					.'find . -type f -exec chmod 777 {} \;;'
					.'chmod 500 '.params::$aFileGame[$server['mta']].'"');

			// Запись данных нового сервера
			$sql->query('UPDATE `servers` set
				`uid`="'.$uid.'",
				`unit`="'.$aSDATA['unit'].'",
				`tarif`="'.$aSDATA['tarif'].'",
				`user`="'.$user['id'].'",
				`address`="'.$aSDATA['address'].'",
				`port`="'.$aSDATA['port'].'",
				`game`="mta",
				`slots`="'.$aSDATA['slots'].'",
				`slots_start`="'.$aSDATA['slots'].'",
				`status`="install",
				`name`="'.$tarif['hostname'].'",
				`pack`="'.$aSDATA['pack'].'",
				`plugins_use`="'.$aSDATA['plugins'].'",
				`ftp_use`="'.$aSDATA['ftp'].'",
				`console_use`="'.$aSDATA['console'].'",
				`stats_use`="'.$aSDATA['stats'].'",
				`copy_use`="'.$aSDATA['copy'].'",
				`web_use`="'.$aSDATA['web'].'",
				`vac`="1",
				`hdd`="'.$aSDATA['hdd'].'",
				`time`="'.$aSDATA['time'].'",
				`date`="'.$start_point.'",
				`test`="'.$aSDATA['test'].'",
				`ram`="'.$aSDATA['ram'].'",
				`core_fix`="'.$aSDATA['core_fix'].'",
				`core_fix_one`="'.$aSDATA['core_fix_one'].'",
				`autostop`="'.$aSDATA['autostop'].'" WHERE `id`="'.$id.'" LIMIT 1');

			// Запись установленных плагинов
			if($aSDATA['plugins'])
			{
				// Массив идентификаторов плагинов
				$aPlugins = sys::b64djs($aSDATA['plugins_install']);

				if(isset($aPlugins[$aSDATA['pack']]))
				{
					$plugins = explode(',', $aPlugins[$aSDATA['pack']]);

					foreach($plugins as $plugin)
						if($plugin)
							$sql->query('INSERT INTO `plugins_install` set `server`="'.$id.'", `plugin`="'.$plugin.'", `time`="'.$start_point.'"');
				}
			}

			// Списание средств с баланса пользователя
			$sql->query('UPDATE `users` set `balance`="'.($user['balance']-$aSDATA['sum']).'" WHERE `id`="'.$user['id'].'" LIMIT 1');

			// Запись получения тестового периода
			if($aSDATA['test'])
			{
				$sql->query('INSERT INTO `tests` set `server`="'.$id.'", `unit`="'.$aSDATA['unit'].'", `game`="mta", `user`="'.$user['id'].'", `time`="'.$start_point.'"');
				$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'buy_server_test'), array('id' => $id)).'", `date`="'.$start_point.'", `type`="buy", `money`="0"');
			}else{
				// Реф. система
				games::part($user['id'], $aSDATA['sum']);

				// Запись логов
				if(!is_array($aSDATA['promo']))
					$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'buy_server'), array('days' => games::parse_day($aSDATA['days'], true), 'money' => $aSDATA['sum'], 'id' => $id)).'", `date`="'.$start_point.'", `type`="buy", `money`="'.$aSDATA['sum'].'"');
				else{
					$sql->query('UPDATE `servers` set `benefit`="'.$aSDATA['time'].'" WHERE `id`="'.$id.'" LIMIT 1');
					$sql->query('INSERT INTO `promo_use` set `promo`="'.$aSDATA['promo']['id'].'", `user`="'.$user['id'].'", `time`="'.$start_point.'"');
					$sql->query('INSERT INTO `logs` set `user`="'.$user['id'].'", `text`="'.sys::updtext(sys::text('logs', 'buy_server_promo'), array('days' => games::parse_day($aSDATA['days'], true), 'money' => $aSDATA['sum'], 'promo' => $aSDATA['promo']['cod'], 'id' => $id)).'", `date`="'.$start_point.'", `type`="buy", `money`="'.$aSDATA['sum'].'"');
				}
			}

			return $id;
		}
    }
?>