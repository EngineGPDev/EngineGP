<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class games
	{
		public static function parse_day($days, $lower = false)
		{
			$aText = array('День', 'Дня', 'Дней');

			if($lower)
				$aText = array('день', 'дня', 'дней');

			return sys::date_decl($days, $aText);
		}

		public static function time($time, $days)
		{
			return $days*86400+$time;
		}

		public static function country($name)
		{
			global $cfg;

			if(file_exists(TPL.'/images/country/'.$name.'.png'))
				return $cfg['url'].'template/images/country/'.$name.'.png';

			return $cfg['url'].'template/images/country/none.png';
		}

		public static function determine($status, $go = false, $tpl = 'content')
		{
			global $html, $text;

			if(!in_array($status, array('install', 'reinstall', 'update', 'recovery', 'overdue', 'blocked')))
				return true;

			$aText = array(
				'install' => 'устанавливается',
				'reinstall' => 'переустанавливается',
				'update' => 'обновляется',
				'recovery' => 'восстанавливается',
				'overdue' => 'не оплачен',
				'blocked' => 'заблокирован'
			);

			$msg = sys::updtext(sys::text('servers', 'determine'), array('status' => $aText[$status]));

			if($go)
				sys::out($msg);

			$html->get('informer');

				$html->set('[class]', 'info_red');
				$html->set('[text]', $msg);

			$html->pack($tpl);

			return false;
		}

		public static function crontab_week($week)
		{
			$aWeek = array();
			$aWeek[1] = isset($week['\'1\'']) ? 'Пн., ' : '';
			$aWeek[2] = isset($week['\'2\'']) ? 'Вт., ' : '';
			$aWeek[3] = isset($week['\'3\'']) ? 'Ср., ' : '';
			$aWeek[4] = isset($week['\'4\'']) ? 'Чт., ' : '';
			$aWeek[5] = isset($week['\'5\'']) ? 'Пт., ' : '';
			$aWeek[6] = isset($week['\'6\'']) ? 'Сб., ' : '';
			$aWeek[7] = isset($week['\'7\'']) ? 'Вс., ' : '';

			$days = '';

			foreach($aWeek as $index => $val)
			{
				if($val == '')
					continue;

				$days .= $val;
			}

			$days = substr($days, 0, -2);

			if($days == '')
				$days = 'Пн., Вт., Ср., Чт., Пт., Сб., Вс.';

			return $days;
		}

		public static function crontab_time($allhour, $hour, $minute)
		{
			if($allhour)
				return 'Каждый час';

			$aHour = array(
				'00', '01', '02',
				'03', '04', '05',
				'06', '07', '08',
				'09', '10', '11',
				'12', '13', '14',
				'15', '16', '17',
				'18', '19', '20',
				'21', '22', '23'
			);

			$aMinute = array(
				'00', '05', '10',
				'15', '20', '25',
				'30', '35', '40',
				'45', '50', '55'
			);

			if(!in_array($hour, $aHour))
				$hour = '00';

			if(!in_array($minute, $aMinute))
				$minute = '00';

			return $hour.':'.$minute;
		}

		public static function crontab($data = array(), $id, $cid)
		{
			global $cfg;

			if($data['allhour'])
				$time = '0 * * * ';
			else{
				$hour = array(
					'00', '01', '02',
					'03', '04', '05',
					'06', '07', '08',
					'09', '10', '11',
					'12', '13', '14',
					'15', '16', '17',
					'18', '19', '20',
					'21', '22', '23'
				);

				$minute = array(
					'00', '05', '10',
					'15', '20', '25',
					'30', '35', '40',
					'45', '50', '55'
				);

				if(!in_array($data['hour'], $hour))
					$data['hour'] = '00';

				if(!in_array($data['minute'], $minute))
					$data['minute'] = '00';

				$time = $data['minute'].' '.$data['hour'].' * * ';
			}

			$week = array();
			$week[1] = isset($data['week']['\'1\'']) ? 1 : 0;
			$week[2] = isset($data['week']['\'2\'']) ? 2 : 0;
			$week[3] = isset($data['week']['\'3\'']) ? 3 : 0;
			$week[4] = isset($data['week']['\'4\'']) ? 4 : 0;
			$week[5] = isset($data['week']['\'5\'']) ? 5 : 0;
			$week[6] = isset($data['week']['\'6\'']) ? 6 : 0;
			$week[7] = isset($data['week']['\'7\'']) ? 7 : 0;

			$check = 0;

			foreach($week as $index => $val)
				$check+= $val;

			if($check == 28 || !$check)
				$week = '*';
			else{
				$weeks = $week[1].','.$week[2].','.$week[3].','.$week[4].','.$week[5].','.$week[6].','.$week[7];
				$weeks = str_replace(array(',0', '0'), '', $weeks);
				$week = $weeks{0} == ',' ? substr($weeks, 1) : $weeks;
			}

			$cron_task = $time.$week.' screen -dmS s'.$id.' bash -c \'cd /var/enginegp && php cron.php '.$cfg['cron_key'].' server_cron '.$id.' '.$cid.'\'';

			return $cron_task;
		}

		public static function parse_tarif($aTarif = array(), $aUnit = array())
		{
			global $cfg, $mcache;

			$nmch = 'parse_tarif_'.$aTarif['id'];

			$cache = $mcache->get($nmch);

			if(is_array($cache))
				return $cache;

			if(isset($aTarif['fps']))
				$aFPS = explode(':', $aTarif['fps']);

			if(isset($aTarif['tickrate']))
				$aTICKRATE = explode(':', $aTarif['tickrate']);

			if(isset($aTarif['ram']))
				$aRAM = explode(':', $aTarif['ram']);

			$fps = '';

			if(isset($aFPS))
				foreach($aFPS as $value)
					$fps .= '<option value="'.$value.'">'.$value.' FPS</option>';

			$tickrate = '';

			if(isset($aTICKRATE))
				foreach($aTICKRATE as $value)
					$tickrate .= '<option value="'.$value.'">'.$value.' TickRate</option>';

			$ram = '';

			if(isset($aRAM))
			{
				if($aTarif['param_fix'])
					foreach($aRAM as $value)
						$ram .= '<option value="'.$value.'">'.$value.' Ram</option>';
				else
					foreach($aRAM as $value)
						$ram .= '<option value="'.$value.'">'.$value.' Ram/Слот</option>';
			}

			$packs = '';
			$aPack = sys::b64djs($aTarif['packs'], true);

			if(is_array($aPack))
				foreach($aPack as $index => $name)
					$packs .= '<option value="'.$index.'">'.$name.'</option>';

			$slots = '';

			for($i = $aTarif['slots_min']; $i <= $aTarif['slots_max']; $i+=1)
				$slots .= '<option value="'.$i.'">'.$i.' шт.</option>';

			$aTime = explode(':', $aTarif['time']);

			$time = games::parse_time($aTime, $aTarif['discount'], $aTarif['id']);

			if($aTarif['test'] AND $aUnit['test'])
				$time .= '<option value="test">Тестовый период '.games::parse_day($aTarif['test']).'</option>';

			$data = array(
				'packs' => $packs,
				'slots' => $slots,
				'time' => $time,
				'fps' => $fps,
				'tickrate' => $tickrate,
				'ram' => $ram
			);

			$mcache->set($nmch, $data, false, 60);

			return $data;
		}

		public static function parse_time($aTime = array(), $discount, $tarif, $type = 'buy')
		{
			global $cfg;

			$time = '';

			$arr = isset(params::$disconunt['service'][$tarif]) ? $tarif : 'time';

			foreach($aTime as $value)
			{
				if(array_key_exists($value, params::$disconunt['service'][$arr][$type]) AND $discount)
				{
					$data = explode(':', params::$disconunt['service'][$arr][$type][$value]);

					// Если наценка
					if($data[0] == '+')
					{
						// Если значение в процентах
						if(substr($data[1], -1) == '%')
							$time .= '<option value="'.$value.'">'.games::parse_day($value).' (Наценка '.$data[1].')</option>';
						else
							$time .= '<option value="'.$value.'">'.games::parse_day($value).' (Наценка '.sys::int($data[1]).' '.$cfg['currency'].')</option>';
					}else{
						// Если значение в процентах
						if(substr($data[1], -1) == '%')
							$time .= '<option value="'.$value.'">'.games::parse_day($value).' (Скидка '.$data[1].')</option>';
						else
							$time .= '<option value="'.$value.'">'.games::parse_day($value).' (Скидка '.sys::int($data[1]).' '.$cfg['currency'].')</option>';
					}
				}else
					$time .= '<option value="'.$value.'">'.games::parse_day($value).'</option>';
			}

			return $time;
		}

		public static function define_period($type, $aD_M, $time = 0)
		{
			global $start_point;

			if($time < $start_point)
				$time = $start_point;

			$day = $type == 'extend' ? date('d', $time) : date('d', $start_point);
			$month = $type == 'extend' ? date('n', $time) : date('n', $start_point);

			$period = $aD_M[$month]-$day;

			if($day > 15)
				$period += $month != 12 ? $aD_M[$month+1] : $aD_M[1];

			return $period+1;
		}

		public static function define_sum($discount, $price, $slots, $time, $type = 'buy')
		{
			global $sql, $user, $cfg, $start_point;

			if($cfg['settlement_period'])
			{
				if($time < $start_point)
					$time = $start_point;

				$day = $type == 'extend' ? date('d', $time) : date('d', $start_point);
				$month = $type == 'extend' ? date('n', $time) : date('n', $start_point);

				$period = params::$aDayMonth[$month]+1-$day;

				$new_month_sum = 0;

				if($day > 15)
					$new_month_sum = ceil($price*$slots);

				$sum = params::$aDayMonth[$month] == $period ? $price*$slots : floor($price*$slots/30*$period)+$new_month_sum;
			}else{
				$sum = floor($price*$slots/30*$time);

				if(array_key_exists($time, params::$disconunt['service']['time'][$type]) AND $discount)
				{
					$data = explode(':', params::$disconunt['service']['time'][$type][$time]);

					// Если наценка
					if($data[0] == '+')
					{
						// Если значение в процентах
						if(substr($data[1], -1) == '%')
							$sum = ceil($sum+$sum/100*intval($data[1]));
						else
							$sum = $sum+intval($data[1]);
					}else{
						// Если значение в процентах
						if(substr($data[1], -1) == '%')
							$sum = ceil($sum-$sum/100*intval($data[1]));
						else
							$sum = $sum-intval($data[1]);
					}
				}
			}

			$sel = $type == 'buy' ? 'rental' : 'extend';

			$sql->query('SELECT `'.$sel.'` FROM `users` WHERE `id`="'.$user['id'].'" LIMIT 1');
			$user = array_merge($user, $sql->get());

			$sum = strpos($user[$sel], '%') ? $sum-$sum/100*$user[$sel] : $sum-$user[$sel];

			if($sum < 0)
				sys::outjs(array('e' => 'Ошибка: сумма за услугу неверна'));

			return $sum;
		}

		public static function define_promo($cod, $data = array(), $discount, $sum, $type = 'buy')
		{
			global $cfg, $sql, $go, $start_point;

			// Проверка формата кода
			if(sys::valid($cod, 'promo'))
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код имеет неверный формат.'));

				return NULL;
			}

			$sql->query('SELECT `id`, `value`, `discount`, `data`, `hits`, `use`, `extend`, `user`, `server` FROM `promo` WHERE `cod`="'.$cod.'" AND `tarif`="'.$data['tarif'].'" AND `time`>"'.$start_point.'" LIMIT 1');

			// Проверка наличия промо-кода
			if(!$sql->num())
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код не найден.'));

				return NULL;
			}

			$promo = $sql->get();

			// Проверка типа при аренде
			if($type == 'buy' AND $promo['extend'])
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код для продления игрового сервера.'));

				return NULL;
			}

			// Проверка типа при продлении
			if($type != 'buy' AND !$promo['extend'])
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код для аренды нового игрового сервера.'));

				return NULL;
			}

			// Проверка доступности на пользователя
			if($promo['user'] AND $data['user'] != $promo['user'])
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код не найден.'));

				return NULL;
			}

			// Проверка доступности на сервер
			if($promo['server'] AND $data['server'] != $promo['server'])
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код не найден.'));

				return NULL;
			}

			$use = $promo['use'] < 1 ? '1' : $promo['use'];

			// Проверка доступности
			$sql->query('SELECT `id` FROM `promo_use` WHERE `promo`="'.$promo['id'].'" LIMIT '.$use);
			if($sql->num() >= $promo['use'])
			{
				if(!$go)
					sys::outjs(array('e' => 'Промо-код использован максимальное количество раз.'));

				return NULL;
			}

			// Данные для сравнения
			$data_promo = sys::b64djs($promo['data'], true);

			$check = 0;

			// Проверка периода
			if(in_array($data['time'], explode(':', $data_promo['time'])))
				$check = 1;

			// Проверка значения FPS
			if((isset($data['fps']) AND isset($data_promo['fps'])) AND in_array($data['fps'], explode(':', $data_promo['fps'])))
				$check+= 1;

			// Проверка значения TICKRATE
			if((isset($data['tickrate']) AND isset($data_promo['tickrate'])) AND in_array($data['tickrate'], explode(':', $data_promo['tickrate'])))
				$check+= 1;

			// Проверка значения RAM
			if((isset($data['ram']) AND isset($data_promo['ram'])) AND in_array($data['ram'], explode(':', $data_promo['ram'])))
				$check+= 1;

			//	Проверка кол-ва слот
			if(isset($data_promo['slots']))
			{
				// Если совпало по перечислению слот (через число:число:число ...)
				if(in_array($data['slots'], explode(':', $data_promo['slots'])))
					$check+= 1;
				else{
					// Если указан диапозон слот
					$aSl = explode('-', $data_promo['slots']);
					if(count($aSl) == 2 AND ($data['slots'] >= $aSl[0] AND $data['slots'] <= $aSl[1]))
						$check+= 1;
				}
			}

			// Проверка совпадений
			if($check < $promo['hits'])
			{
				if(!$go)
					sys::outjs(array('e' => 'Условия для данного промо-кода не выполнены.'));

				return NULL;
			}

			// Если скидка
			if($promo['discount'])
			{
				// Если не суммировать скидки
				if(!$cfg['promo_discount'])
				{
					if(array_key_exists($data['time'], params::$disconunt['service']['time'][$type]) AND $discount)
					{
						$data = explode(':', params::$disconunt['service']['time'][$type][$data['time']]);

						// Если скидка
						if($data[0] == '-')
						{
							// Если значение в процентах
							if(substr($data[1], -1) == '%')
								$sum = ceil($sum+$sum/100*intval($data[1]));
							else
								$sum = $sum+intval($data[1]);
						}
					}
				}

				// Пересчет суммы
				if(substr($promo['value'], -1) == '%')
					$sum = $sum-ceil($sum/100*intval($promo['value']));
				else
					$sum = $sum-intval($promo['value']);

				if(!$go)
					sys::outjs(array('sum' => $sum, 'discount' => 1, 'cur' => $cfg['currency']));

				return array('id' => $promo['id'], 'cod' => $cod, 'sum' => $sum);

			}

			// Подарочные дни
			$days = intval($promo['value']);

			if(!$go)
				sys::outjs(array('days' => games::parse_day($days)));

			return array('id' => $promo['id'], 'cod' => $cod, 'days' => $days);
		}

		public static function info_tarif($game, $tarif, $param)
		{
			if($game == 'cs')
				return $tarif.' / '.$param['fps'].' FPS';

			if($game == 'mc')
				return $tarif.' / '.$param['ram'].' RAM';

			if($game == 'cssold')
				return $tarif.' / '.$param['fps'].' FPS / '.$param['tickrate'].' TickRate';

			if(in_array($game, array('css', 'csgo')))
				return $tarif.' / '.$param['tickrate'].' TickRate';

			return $tarif;
		}

		public static function maplist($id, $unit, $folder, $map, $go, $mcache = '', $ctrl = false)
		{
			global $user, $sql;

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
			{
				if($go)
					sys::outjs(array('e' => sys::text('error', 'ssh')), $mcache);

				sys::outjs(array('maps', '<option value="0">unknown</option>'));
			}

			// Генерация списка карт
			$aMaps = array_diff(explode("\n", $ssh->get('cd '.$folder.' && du -ah | grep -e "\.bsp$" | awk \'{print $2}\'')), array(''));

			// Удаление ".bsp"
			$aMaps = str_ireplace(array('./', '.bsp'), '', $aMaps);

			if($go)
			{
				$map = str_replace('|', '/', urldecode($map));

				$sqlq = $ctrl ? 'control_' : '';

				// Проверка наличия выбранной карты
				if(in_array($map, $aMaps))
					$sql->query('UPDATE `'.$sqlq.'servers` set `map_start`="'.$map.'" WHERE `id`="'.$id.'" LIMIT 1');

				sys::outjs(array('s' => 'ok'), $mcache);
			}

			sort($aMaps);
			reset($aMaps);

			$ismap = in_array($map, $aMaps);
			$maps = $ismap ? '<option value="'.str_replace('/', '|', $map).'">'.$map.'</option>' : '<option value="">Указанная ранее карта "'.$map.'" не найдена</option>';

			// Удаление стартовой карты
			if($ismap)
				unset($aMaps[array_search($map, $aMaps)]);

			foreach($aMaps as $map)
				$maps .= '<option value="'.str_replace('/', '|', $map).'">'.$map.'</option>';

			sys::outjs(array('maps' => $maps));
		}

		public static function owners($aRights)
		{
			if(array_search(0, $aRights))
				return 'Есть ограничения в доступе.';

			return 'Выданы все права.';
		}

		public static function part($uid, $money)
		{
			global $cfg, $sql, $start_point;

			if($cfg['part'])
				return NULL;

			$sql->query('SELECT `part` FROM `users` WHERE `id`="'.$uid.'" LIMIT 1');
			$user = $sql->get();

			if(!$user['part'])
				return NULL;

			$sql->query('SELECT `balance`, `part_money` FROM `users` WHERE `id`="'.$user['part'].'" LIMIT 1');
			if(!$sql->num())
				return NULL;

			$user = array_merge($user, $sql->get());

			$sum = round($money/100*$cfg['part_proc'], 2);

			if($cfg['part_money'])
				$sql->query('UPDATE `users` set `part_money`="'.($user['part_money']+$sum).'" WHERE `id`="'.$user['part'].'" LIMIT 1');
			else	
				$sql->query('UPDATE `users` set `balance`="'.($user['balance']+$sum).'" WHERE `id`="'.$user['part'].'" LIMIT 1');

			$sql->query('INSERT INTO `logs` set `user`="'.$user['part'].'", `text`="'.sys::updtext(sys::text('logs', 'part'),
				array('part' => $uid, 'money' => $sum)).'", `date`="'.$start_point.'", `type`="part", `money`="'.$sum.'"');

			return NULL;
		}

		public static function map($map, $aMaps)
		{
			if(!is_array($aMaps))
				$aMaps = explode("\n", str_ireplace(array('./', '.bsp'), '', $aMaps));

			if(in_array($map, $aMaps))
				return false;

			return true;
		}

		public static function mapsql($arr = array())
		{
			$sql = 'AND (';

			foreach($arr as $map)
				$sql .= ' `name` REGEXP FROM_BASE64(\''.base64_encode('^'.$map.'\_').'\') OR';

			return $sql == 'AND (' ? '' : substr($sql, 0, -3).')';
		}

		public static function iptables_whois($mcache)
		{
			$address = isset($_POST['address']) ? trim($_POST['address']) : sys::outjs(array('info' => 'Не удалось получить информацию.'), $mcache);

			if(sys::valid($address, 'ip'))
				sys::outjs(array('e' => sys::text('servers', 'firewall')), $mcache);

			include(LIB.'geo.php');

			$SxGeo = new SxGeo(DATA.'SxGeoCity.dat');

			$data = $SxGeo->getCityFull($address);

			$info = 'Информация об IP адресе:';

			if($data['country']['name_ru'] != '')
			{
				$info .= '<p>Страна: '.$data['country']['name_ru'];

				if($data['city']['name_ru'] != '')
					$info .= '<p>Город: '.$data['city']['name_ru'];

				$info .= '<p>Подсеть: '.sys::whois($address);

			}else
				$info = 'Не удалось получить информацию.';

			sys::outjs(array('info' => $info), $mcache);
		}

		public static function iptables($id, $action, $source, $dest, $unit, $snw = false, $ssh = false)
		{
			global $cfg, $sql, $start_point;

			if(!$ssh)
			{
				$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$unit.'" LIMIT 1');
				$unit = $sql->get();

				include(LIB.'ssh.php');

				if(!$ssh->auth($unit['passwd'], $unit['address']))
					return array('e' => sys::text('all', 'ssh'));
			}

			switch($action)
			{
				case 'block':
					if(sys::valid($source, 'ip'))
						return array('e' => sys::text('servers', 'firewall'));

					// Если подсеть
					if($snw)
					{
						$source = sys::whois($source);

						if($source == 'не определена')
							return array('e' => 'Не удалось определить подсеть для указанного адреса.');
					}

					$sql->query('SELECT `id` FROM `firewall` WHERE `sip`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

					// Если такое правило уже добавлено или указан адрес сайта (ПУ)
					if($sql->num() || ($source == $cfg['ip'] || $source == $cfg['subnet']))
						return array('s' => 'ok');

					$sql->query('INSERT INTO `firewall` set `sip`="'.$source.'", `dest`="'.$dest[0].':'.$dest[1].'", `server`="'.$id.'", `time`="'.$start_point.'"');

					$line = $sql->id();

					$rule = 'iptables -I INPUT -s '.$source.' -p udp -d '.$dest[0].' --dport '.$dest[1].' -j DROP;';

					$ssh->set($rule.' echo -e "#'.$line.';\n'.$rule.'" >> /root/'.$cfg['iptables']);

					return array('s' => 'ok');

				case 'unblock':
					if(!is_numeric($source) AND sys::valid($source, 'ip'))
						return array('e' => sys::text('servers', 'firewall'));

					if(is_numeric($source))
					{
						$sql->query('SELECT `id`, `sip` FROM `firewall` WHERE `id`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

						// Если такое правило отсутствует
						if(!$sql->num())
							return array('s' => 'ok');
					}else{
						$sql->query('SELECT `id`, `sip` FROM `firewall` WHERE `sip`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

						// Если одиночный адрес не найден, проверить на блокировку подсети
						if(!$sql->num())
						{
							$source = sys::whois($source);

							$sql->query('SELECT `id` FROM `firewall` WHERE `sip`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

							if($sql->num())
							{
								$firewall = $sql->get();

								return array('i' => 'Указанный адрес входит в заблокированную подсеть, разблокировать подсеть?', 'id' => $firewall['id']);
							}

							return array('s' => 'ok');
						}
					}

					$firewall = $sql->get();

					$ssh->set('iptables -D INPUT -s '.$firewall['sip'].' -p udp -d '.$dest[0].' --dport '.$dest[1].' -j DROP;'
							.'sed "`nl '.$cfg['iptables'].' | grep \"#'.$firewall['id'].'\" | awk \'{print $1","$1+1}\'`d" '.$cfg['iptables'].' > '.$cfg['iptables'].'_temp; cat '.$cfg['iptables'].'_temp > '.$cfg['iptables'].'; rm '.$cfg['iptables'].'_temp');

					$sql->query('DELETE FROM `firewall` WHERE `id`="'.$firewall['id'].'" LIMIT 1');

					return array('s' => 'ok');

				case 'remove':
					$sql->query('SELECT `id`, `sip`, `dest` FROM `firewall` WHERE `server`="'.$id.'"');

					$aRule = array();

					while($firewall = $sql->get())
					{
						list($ip, $port) = explode(':', $firewall['dest']);

						$aRule[$firewall['id']] = 'iptables -D INPUT -s '.$firewall['sip'].' -p udp -d '.$ip.' --dport '.$port.' -j DROP;';
					}

					$nRule = count($aRule);

					if(!$nRule)
						return NULL;

					$cmd = '';

					foreach($aRule as $line => $rule)
						$cmd .= $rule.'sed "`nl '.$cfg['iptables'].' | grep "#'.$line.'" | awk \'{print $1","$1+1}\'`d" '.$cfg['iptables'].' > '.$cfg['iptables'].'_temp; cat '.$cfg['iptables'].'_temp > '.$cfg['iptables'].'; rm '.$cfg['iptables'].'_temp';

					$ssh->set($cmd);

					$sql->query('DELETE FROM `firewall` WHERE `server`="'.$id.'" LIMIT '.$nRule);

					return array('s' => 'ok');
			}
		}
	}
?>