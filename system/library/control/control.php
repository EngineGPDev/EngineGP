<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class ctrl
	{
		public static function status($status)
		{
			switch($status)
			{
				case 'working':
					return 'работает';

				case 'reboot':
					return 'перезагружается';

				case 'error':
					return 'не отвечает';

				case 'install':
					return 'настраивается';

				case 'overdue':
					return 'просрочен';

				case 'blocked':
					return 'заблокирован';
			}
		}

		public static function buttons($id, $status)
		{
			global $html;

			$html->arr['btn'] = '';

			if($status == 'working')
			{
				$html->get('restart', 'sections/control/buttons');
					$html->set('id', $id);
				$html->pack('btn');

				return $html->arr['btn'];
			}

			return '';
		}

		public static function resources($id)
		{
			global $sql;

			include(LIB.'ssh.php');

			$aData = array(
				'cpu' => '0%',
				'ram' => '0%',
				'hdd' => '0%'
			);

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
			$ctrl = $sql->get();

			if(!$ssh->auth($ctrl['passwd'], $ctrl['address']))
				sys::outjs($aData);

			$data = $ssh->get('echo `cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'; cat /proc/meminfo | grep MemFree | awk \'{print $2}\'; cat /proc/meminfo | grep Buffers | awk \'{print $2}\'; cat /proc/meminfo | grep Cached | grep -v SwapCached | awk \'{print $2}\'`');
			$aData['ram'] = ceil(ctrl::ram_load($data)).'%';

			$aData['hdd'] = trim($ssh->get('df -h | awk \'/rootfs/ {print $5}\''));

			$aData['cpu'] = ctrl::cpu_load($ssh->get('echo "`ps -A -o pcpu | tail -n+2 | paste -sd+ | bc | awk \'{print $0}\'` `cat /proc/cpuinfo | grep processor | wc -l | awk \'{print $1}\'`"')).'%';

			sys::outjs($aData);
		}

		public static function update_info($id)
		{
			global $sql;

			include(LIB.'ssh.php');

			$aData = array(
				'cpu' => 'произошла ошибка',
				'ram' => 'произошла ошибка',
				'hdd' => 'произошла ошибка'
			);

			$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
			$ctrl = $sql->get();

			if(!$ssh->auth($ctrl['passwd'], $ctrl['address'].':22'))
				sys::outjs($aData);

			$data = $ssh->get('cat /proc/meminfo | grep MemTotal | cut -d \' \' -f 2-');
			$aData['ram'] = $data.' ('.(round(sys::int($data)/1024/1024, 2)).'Gb)';

			$aData['hdd'] = trim($ssh->get('df -h | awk \'/rootfs/ {print $2}\''));

			$aCPU = explode("\n", trim($ssh->get('cat /proc/cpuinfo | grep -c processor && cat /proc/cpuinfo | grep "MHz" | awk \'{print $4}\' | head -n 1')));
			
			$aData['cpu'] = $aCPU[0].'x'.round($aCPU[1], 0).' MHz ['.trim($ssh->get('cat /proc/cpuinfo | grep -m 1 "model name" | cut -d \' \' -f 3-')).']';

			sys::outjs($aData);
		}

		public static function update_status($id, $ssh = false)
		{
			global $cfg, $sql, $start_point, $mcache;

			if(!$ssh)
				include(LIB.'ssh.php');

			$sql->query('SELECT `address`, `passwd`, `time`, `overdue`, `block`, `status` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
			$ctrl = $sql->get();

			$status = $ctrl['status'];

			if($ctrl['status'] == 'blocked' && $ctrl['block'] < $start_point)
				$sql->query('UPDATE `control` set `block`="0", `status`="working" WHERE `id`="'.$id.'" LIMIT 1');

			if($ctrl['status'] == 'blocked')
				return 'blocked';

			// Если аренда закончилась и сервер просрочен длительное время
			if($ctrl['time'] < $start_point && $ctrl['status'] == 'overdue' && ($ctrl['overdue']+$cfg['control_delete']*86400) < $start_point)
			{
				$sql->query('UPDATE `control` set `user`="-1" WHERE `id`="'.$id.'" LIMIT 1');

				return 'delete';
			}

			// Если аренда закончилась, а услуга не просрочена
			if($ctrl['time'] < $start_point && !in_array($ctrl['status'], array('overdue', 'blocked')))
			{
				$sql->query('UPDATE `control` set `status`="overdue" WHERE `id`="'.$id.'" LIMIT 1');

				$status = 'overdue';
			}

			// Если аренда не закончилась, а услуга просрочена
			if($ctrl['time'] > $start_point && $ctrl['status'] == 'overdue')
				$sql->query('UPDATE `control` set `status`="working" WHERE `id`="'.$id.'" LIMIT 1');

			if(in_array($ctrl['status'], array('working', 'error')))
			{
				$status = 'working';
				if(!$ssh->auth($ctrl['passwd'], $ctrl['address']))
					$status = 'error';

				$sql->query('UPDATE `control` set `status`="'.$status.'" WHERE `id`="'.$id.'" LIMIT 1');
			}

			if($ctrl['status'] == 'reboot' && !$mcache->get('reboot_control_'.$id))
			{
				if($ssh->auth($ctrl['passwd'], $ctrl['address']))
				{
					$sql->query('UPDATE `control` set `status`="working" WHERE `id`="'.$id.'" LIMIT 1');

					$status = 'working';
				}
			}

			$time_end = $ctrl['status'] == 'overdue' ? 'Удаление через: '.sys::date('min', $ctrl['overdue']+$cfg['control_delete']*86400) : 'Осталось: '.sys::date('min', $ctrl['time']);

			$aData = array(
				'time' => sys::today($ctrl['time']),
				'time_end' => $time_end,
				'buttons' => ctrl::buttons($id, $status),
				'status' => ctrl::status($status)
			);

			return $aData;
		}

		public static function ram_load($data)
		{
			$aData = explode(' ', $data);

			return ceil(($aData[0]-($aData[1]+$aData[2]+$aData[3]))*100/$aData[0]);
		}

		public static function cpu_load($data)
		{
			$aData = explode(' ', $data);

			$load = ceil($aData[0]/$aData[1]);

			return $load > 100 ? 100 : $load;
		}

		public static function nav($server, $id, $sid, $active)
		{
			global $cfg, $html, $sql, $mcache, $start_point;

			$aUnit = array('index', 'console', 'settings', 'plugins', 'filetp', 'copy', 'boost');

			$html->get('gmenu', 'sections/control/servers/'.$server['game']);

				$html->set('id', $id);
				$html->set('server', $sid);
				$html->set('home', $cfg['http']);

				foreach($aUnit as $unit)
					if($unit == $active) $html->unit($unit, 1); else $html->unit($unit);

			$html->pack('main');

			$html->get('vmenu', 'sections/control/servers/'.$server['game']);

				$html->set('id', $id);
				$html->set('server', $sid);
				$html->set('home', $cfg['http']);

				foreach($aUnit as $unit)
					if($unit == $active) $html->unit($unit, 1); else $html->unit($unit);

			$html->pack('vmenu');

			return NULL;
		}

		public static function route($server, $inc, $go)
		{
			global $device, $start_point;

			if(in_array($server['status'], array('install', 'reinstall', 'update', 'recovery')))
			{
				if($go)
					sys::out('Раздел недоступен');

				return SEC.'control/servers/noaccess.php';
			}

			if(!file_exists(SEC.'control/servers/'.$server['game'].'/'.$inc.'.php'))
				return SEC.'control/servers/'.$server['game'].'/index.php';

			return SEC.'control/servers/'.$server['game'].'/'.$inc.'.php';
		}

		public static function cpulist($unit, $core, $count = false)
		{
			global $device, $start_point;

			include(LIB.'ssh.php');

			if(!$ssh->auth($unit['passwd'], $unit['address']))
			{
				if($count)
					return 1;

				$out = $core ? '<option value="0">Автоматическое определение</option><option value="1">1 ядро/поток</option>' : '<option value="'.$core.'">'.$core.' ядро/поток</option><option value="0">Автоматическое определение</option>';

				sys::outjs(array('core_fix' => $core));
			}

			$n = sys::int($ssh->get('cat /proc/cpuinfo | grep "cpu MHz" | wc -l'));

			if($count)
				return $n;

			$list = '<option value="0">Автоматическое определение</option>';

			for($i = 1; $i <= $n; $i+=1)
				$list .= '<option value="'.$i.'">'.$i.' ядро/поток</option>';

			sys::outjs(array('core_fix' => str_replace($core.'"', $core.'" selected="select"', $list)));
		}

		public static function iptables($id, $action, $source, $dest, $unit, $snw = false, $ssh = false)
		{
			global $cfg, $sql, $start_point;

			if(!$ssh)
			{
				$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$unit.'" LIMIT 1');
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

					$sql->query('SELECT `id` FROM `control_firewall` WHERE `sip`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

					// Если такое правило уже добавлено или указан адрес сайта (ПУ)
					if($sql->num() || ($source == $cfg['ip'] || $source == $cfg['subnet']))
						return array('s' => 'ok');

					$sql->query('INSERT INTO `control_firewall` set `sip`="'.$source.'", `dest`="'.$dest[0].':'.$dest[1].'", `server`="'.$id.'", `time`="'.$start_point.'"');

					$line = $sql->id();

					$rule = 'iptables -I INPUT -s '.$source.' -p udp -d '.$dest[0].' --dport '.$dest[1].' -j DROP;';

					$ssh->set($rule.' echo -e "#'.$line.';\n'.$rule.'" >> /root/'.$cfg['iptables']);

					return array('s' => 'ok');

				case 'unblock':
					if(!is_numeric($source) AND sys::valid($source, 'ip'))
						return array('e' => sys::text('servers', 'firewall'));

					if(is_numeric($source))
					{
						$sql->query('SELECT `id`, `sip` FROM `control_firewall` WHERE `id`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

						// Если такое правило отсутствует
						if(!$sql->num())
							return array('s' => 'ok');
					}else{
						$sql->query('SELECT `id`, `sip` FROM `control_firewall` WHERE `sip`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

						// Если одиночный адрес не найден, проверить на блокировку подсети
						if(!$sql->num())
						{
							$source = sys::whois($source);

							$sql->query('SELECT `id` FROM `control_firewall` WHERE `sip`="'.$source.'" AND `server`="'.$id.'" LIMIT 1');

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

					$sql->query('DELETE FROM `control_firewall` WHERE `id`="'.$firewall['id'].'" LIMIT 1');

					return array('s' => 'ok');

				case 'remove':
					$sql->query('SELECT `id`, `sip`, `dest` FROM `control_firewall` WHERE `server`="'.$id.'"');

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

					$sql->query('DELETE FROM `control_firewall` WHERE `server`="'.$id.'" LIMIT '.$nRule);

					return array('s' => 'ok');
			}
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

			$cron_task = $time.$week.' screen -dmS s'.$id.' bash -c \'cd /var/enginegp && php cron.php '.$cfg['cron_key'].' control_server_cron '.$id.' '.$cid.'\'';

			return $cron_task;
		}
	}
?>