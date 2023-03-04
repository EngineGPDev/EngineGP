<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    class tarif extends tarifs
    {
        public static function extend($options, $server, $tarif_name, $sid)
        {
			global $cfg, $sql, $html, $start_point;

			tarifs::extend_address($server['game'], $sid);

			$html->get('extend', 'sections/servers/games/tarif');

				if(isset($html->arr['extend_address']))
				{
					$html->unit('extend_address', 1);
						$html->set('extend_address', $html->arr['extend_address']);
				}else
					$html->unit('extend_address');

				$html->set('id', $sid);
				$html->set('time', sys::date('min', $server['time']));
				$html->set('options', '<option value="0">Выберете период продления</option>'.$options);
				$html->set('slots', $server['slots']);
				$html->set('info', '');
				$html->set('tarif', $tarif_name);
				$html->set('cur', $cfg['currency']);

			$html->pack('main');

			return NULL;
		}

        public static function extend_sp($server, $tarif, $sid)
        {
			global $cfg, $sql, $html, $start_point;

			tarifs::extend_address($server['game'], $sid);

			$sum = $tarif['slots'] ? $tarif['price'] : $tarif['price']*$server['slots'];

			$html->get('extend_sp', 'sections/servers/games/tarif');

				if(isset($html->arr['extend_address']))
				{
					$html->unit('extend_address', 1);
						$html->set('extend_address', $html->arr['extend_address']);
				}else
					$html->unit('extend_address');

				$html->set('id', $sid);
				$html->set('time', sys::date('min', $server['time']));
				$html->set('date', $server['time'] > $start_point ? 'Сервер продлен до: '.date('d.m.Y', $server['time']) : 'Текущая дата: '.date('d.m.Y', $start_point));
				$html->set('options', '<option value="0">Выберете период продления</option>'.$options);
				$html->set('slots', $server['slots']);
				$html->set('info', '');
				$html->set('tarif', $tarif['name']);
				$html->set('sum', $sum);
				$html->set('cur', $cfg['currency']);

			$html->pack('main');

			return NULL;
		}

        public static function plan()
        {
			return NULL;
		}

		public static function unit($server, $unit_name, $tarif_name, $sid)
        {
			global $cfg, $sql, $html;

			if(!$cfg['change_unit'][$server['game']])
				return NULL;

			$tarifs = $sql->query('SELECT `unit` FROM `tarifs` WHERE `game`="'.$server['game'].'" AND `name`="'.$tarif_name.'" AND `id`!="'.$server['tarif'].'" AND `show`="1" ORDER BY `unit`');
			if(!$sql->num($tarifs))
				return NULL;

			$units = 0;

			$options = '<option value="0">Выберете новую локацию</option>';

			while($tarif = $sql->get($tarifs))
			{
				$sql->query('SELECT `id`, `name` FROM `units` WHERE `id`="'.$tarif['unit'].'" AND `show`="1" LIMIT 1');
				if(!$sql->num())
					continue;

				$unit = $sql->get();

				$options .= '<option value="'.$unit['id'].'">'.$unit['name'].'</option>';

				$units+=1;
			}

			if(!$units)
				return NULL;

			$html->get('unit', 'sections/servers/games/tarif');

				$html->set('id', $sid);
				$html->set('options', $options);
				$html->set('slots', $server['slots']);
				$html->set('info', '');
				$html->set('unit', $unit_name);
				$html->set('tarif', $tarif_name);

			$html->pack('main');

			return NULL;
		}

		public static function unit_new($tarif, $unit, $server, $mcache)
		{
			global $ssh, $sql, $user, $start_point;

			// Проверка ssh соединения с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				sys::outjs(array('e' => sys::text('error', 'ssh')));

			// Директория сборки
			$path = $tarif['path'].$tarif['pack'];

			// Директория игрового сервера
			$install = $tarif['install'].$server['uid'];

			// Пользователь сервера
			$uS = 'server'.$server['uid'];

			$ssh->set('mkdir '.$install.';' // Создание директории
					.'useradd -d '.$install.' -g servers -u '.$server['uid'].' '.$uS.';' // Создание пользователя сервера на локации
					.'chown '.$uS.':1000 '.$install.';' // Изменение владельца и группы директории
					.'cd '.$install.' && sudo -u '.$uS.' screen -dmS i_'.$server['uid'].' cp -r '.$path.'/. .'); // Копирование файлов сборки для сервера

			$address = explode(':', $server['address']);

			$fix_one = $tarif['core_fix'] ? 1 : 0;

			// Обновление данных нового сервера
			$sql->query('UPDATE `servers` set
				`unit`="'.$tarif['unit'].'",
				`tarif`="'.$tarif['id'].'",
				`address`="'.$server['address'].'",
				`port`="'.$address[1].'",
				`status`="install",
				`name`="'.$tarif['hostname'].'",
				`pack`="'.$tarif['pack'].'",
				`map_start`="'.$tarif['map'].'",
				`hdd`="'.$tarif['hdd'].'",
				`time`="'.$tarif['time'].'",
				`autostop`="'.$tarif['autostop'].'",
				`core_fix`="'.$tarif['core_fix'].'",
				`core_fix_one`="'.$fix_one.'",
				`reinstall`="'.$start_point.'" WHERE `id`="'.$server['id'].'" LIMIT 1');

			// Запись установленных плагинов
			if($tarif['plugins'])
			{
				// Массив идентификаторов плагинов
				$aPlugins = sys::b64js($tarif['plugins_install']);

				if(isset($aPlugins[$tarif['pack']]))
				{
					$plugins = explode(',', $aPlugins[$tarif['pack']]);

					foreach($plugins as $plugin)
						if($plugin)
							$sql->query('INSERT INTO `plugins_install` set `server`="'.$server['id'].'", `plugin`="'.$plugin.'", `time`="'.$start_point.'"');
				}
			}

			return NULL;
		}
    }
?>