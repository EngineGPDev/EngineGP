<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class server_delete extends cron
	{
		function __construct()
		{
			global $cfg, $sql, $argv;

			$sql->query('SELECT `id`, `uid`, `user`, `unit`, `tarif`, `game`, `slots`, `address`, `ddos` FROM `servers` WHERE `id`="'.$argv[3].'" AND `user`="-1" LIMIT 1');

			if(!$sql->num())
				return NULL;

			$server = $sql->get();

			if(!$server['uid'])
				return NULL;

			$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
			$tarif = $sql->get();

			$sql->query('SELECT `address`, `passwd`, `sql_login`, `sql_passwd`, `sql_port`, `sql_ftp` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
			$unit = $sql->get();

			include(LIB.'ssh.php');

			// Проверка ssh соединения с локацией
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				return NULL;

			// Убить процессы
			$ssh->set('kill -9 `ps aux | grep s_'.$server['uid'].' | grep -v grep | awk '."'{print $2}'".' | xargs;'
				.'lsof -i@:'.$server['address'].' | awk '."'{print $2}'".' | xargs`; sudo -u server'.$server['uid'].' screen -wipe');

			// Директория игрового сервера
			$install = $tarif['install'].$server['uid'];

			$copys = 'screen -dmS r_copy_'.$server['uid'].' sh -c "';

			$scopy = $sql->query('SELECT `id`, `name` FROM `copy` WHERE `server`="'.$server['id'].'"');
			while($copy = $sql->get($scopy))
			{
				$copys .= 'rm /copy/'.$copy['name'].'.tar;';

				$sql->query('DELETE FROM `copy` WHERE `id`="'.$copy['id'].'" LIMIT 1');
			}

			$copys .= '";';

			$ssh->set($copys // Удаление резервных копий
					.'screen -dmS r_'.$server['uid'].' sh -c "rm -r '.$install.';' // Удаление директории сервера
					.'userdel server'.$server['uid'].'"'); // Удаление пользователя сервера c локации

			// Удаление ftp доступа
			$qSql = 'DELETE FROM users WHERE username=\''.$server['uid'].'\';'
                    .'DELETE FROM quotalimits WHERE name=\''.$server['uid'].'\';'
                    .'DELETE FROM quotatallies WHERE name=\''.$server['uid'].'\'';

            $ssh->set('screen -dmS ftp'.$server['uid'].' mysql -P '.$unit['sql_port'].' -u'.$unit['sql_login'].' -p'.$unit['sql_passwd'].' --database '.$unit['sql_ftp'].' -e "'.$qSql.'"');

			include(LIB.'games/games.php');

			// Очистка правил FireWall
			games::iptables($server['id'], 'remove', NULL, NULL, NULL, false, $ssh);

			// Очистка правил FireWall GEO
			if($server['ddos'])
			{
				$geo = $cfg['iptables'].'_geo';

				$country = $server['ddos'] == 2 ? 'AM,BY,UA,RU,KZ' : 'UA,RU';

				$ssh->set('iptables -D INPUT -p udp -d '.$ip.' --dport '.$port.' -m geoip ! --source-country '.$country.' -j DROP;'
					.'sed "`nl '.$geo.' | grep \"#'.$id.'\" | awk \'{print $1","$1+1}\'`d" '.$geo.' > '.$geo.'_temp; cat '.$geo.'_temp > '.$geo.'; rm '.$geo.'_temp;');
			}

			// Удаление заданий из crontab
			$sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
			$panel = $sql->get();

			if(!$ssh->auth($panel['passwd'], $panel['address']))
				return NULL;

			$crons = $sql->query('SELECT `id`, `cron` FROM `crontab` WHERE `server`="'.$server['id'].'"');
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

				$sql->query('DELETE FROM `crontab` WHERE `id`="'.$cron['id'].'" LIMIT 1');
			}

			// Обновление данных выделенного адреса
			$sql->query('SELECT `id`, `aid` FROM `address_buy` WHERE `server`="'.$server['id'].'" LIMIT 1');
			if($sql->num())
			{
				$add = $sql->get();

				$sql->query('UPDATE `address` set `buy`="0" WHERE `id`="'.$add['aid'].'" LIMIT 1');
				$sql->query('DELETE FROM `address_buy` WHERE `id`="'.$add['id'].'" LIMIT 1');
			}

			include(DATA.'web.php');

			$sql->query('SELECT `id` FROM `servers` WHERE `id`!="'.$server['id'].'" AND `user`="'.$server['user'].'" AND `unit`="'.$server['unit'].'" LIMIT 1');
			if($sql->num())
			{
				$server_sec = $sql->get();

				// Подготовка к удалению доп. услуги или обновление данных
				$webs = $sql->query('SELECT `id`, `type` FROM `web` WHERE `server`="'.$server['id'].'"');
				while($web = $sql->get($webs))
				{
					if($aWebInstall[$server['game']][$web['type']] == ('unit' || 'user'))
						$sql->query('UPDATE `web` set `server`="'.$server_sec['id'].'" WHERE `id`="'.$web['id'].'" LIMIT 1');
					else
						$sql->query('UPDATE `web` set `user`="0" WHERE `id`="'.$web['id'].'" LIMIT 1');
				}
			}else
				$sql->query('UPDATE `web` set `user`="0" WHERE `server`="'.$server['id'].'"');

			// Удаление различной информации игрового сервера
			$sql->query('DELETE FROM `admins_'.$server['game'].'` WHERE `server`="'.$server['id'].'"');
			$sql->query('DELETE FROM `plugins_install` WHERE `server`="'.$server['id'].'"');
			$sql->query('DELETE FROM `owners` WHERE `server`="'.$server['id'].'"');
			$sql->query('DELETE FROM `graph` WHERE `server`="'.$server['id'].'"');
			$sql->query('DELETE FROM `graph_day` WHERE `server`="'.$server['id'].'"');
			$sql->query('DELETE FROM `graph_hour` WHERE `server`="'.$server['id'].'"');
			$sql->query('DELETE FROM `servers` WHERE `id`="'.$server['id'].'" LIMIT 1');

			$sql->query('INSERT INTO `logs_sys` set `user`="0", `server`="'.$argv[3].'", `text`="Удаление игрового сервера #'.$argv[3].' ('.$server['game'].') unit: #'.$server['unit'].', tarif: #'.$server['tarif'].', slots: #'.$server['slots'].'", `time`="'.$start_point.'"');

			return NULL;
		}
	}
?>