<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `slots`, `map_start`, `vac`, `fastdl`, `autorestart`, `fps`, `tickrate`, `core_fix` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
	$server = array_merge($server, $sql->get());
	
	$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$unit = $sql->get();

	include(LIB.'games/games.php');

	// Вывод списка карт
	if(isset($url['maps']))
		games::maplist($sid, $unit, '/servers/'.$server['uid'].'/cstrike/maps', $server['map_start'], false);

	// Вывод списка потоков
	if(isset($url['core']))
		ctrl::cpulist($unit, $server['core_fix']);

	// Сохранение
	if($go AND $url['save'])
	{
		$value = isset($url['value']) ? sys::int($url['value']) : sys::outjs(array('s' => 'ok'), $nmch);
		
		switch($url['save'])
		{
			case 'map':
				$map = isset($url['value']) ? trim($url['value']) : sys::outjs(array('s' => 'ok'), $nmch);

				if($map != $server['map_start'])
					games::maplist($sid, $unit, '/servers/'.$server['uid'].'/cstrike/maps', $map, true, $nmch, true);

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'address':
				if($server['status'] != 'off')
					sys::outjs(array('e' => 'Необходимо выключить игровой сервер'), $nmch);

				$address = isset($_POST['address']) ? trim($_POST['address']) : $server['address'];

				if(sys::valid($address, 'other', $aValid['address']))
					sys::outjs(array('e' => 'Адрес игрового сервера имеет неверный формат'), $nmch);

				$sql->query('SELECT `id` FROM `control_servers` WHERE `unit`="'.$id.'" AND `address`="'.$address.'" LIMIT 1');
				if($sql->num())
					sys::outjs(array('e' => 'Данный адрес занят другим сервером'), $nmch);

				if($address != $server['address'])
					$sql->query('UPDATE `control_servers` set `address`="'.$address.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'vac':
				if($value != $server['vac'])
					$sql->query('UPDATE `control_servers` set `vac`="'.$value.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'core_fix':
				$n = ctrl::cpulist($unit, $server['core_fix'], true);

				if($value > $n)
					sys::outjs(array('e' => 'На физическом сервере нет такого ядра/потока'), $nmch);

				if($value < 0)
					$value = 0;

				if($value != $server['core_fix'])
					$sql->query('UPDATE `control_servers` set `core_fix`="'.$value.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'slots':
				$slots = $value > 64 ? 64 : $value;
				$slots = $value < 2 ? 2 : $slots;

				if($slots != $server['slots'])
					$sql->query('UPDATE `control_servers` set `slots`="'.$slots.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'autorestart':
				if($value != $server['autorestart'])
					$sql->query('UPDATE `control_servers` set `autorestart`="'.$value.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'fps':
				if(in_array($value, array('300', '500', '1100')))
					$sql->query('UPDATE `control_servers` set `fps`="'.$value.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'tickrate':
				if(in_array($value, array('33', '66', '100')))
					$sql->query('UPDATE `control_servers` set `tickrate`="'.$value.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'fastdl':
				include(LIB.'ssh.php');

				if(!$ssh->auth($unit['passwd'], $unit['address']))
					sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

				if($value)
				{
					$fastdl = 'sv_downloadurl "http://'.$unit['address'].':8080/fast_'.$server['uid'].'"'.PHP_EOL
							.'sv_consistency 1'.PHP_EOL
							.'sv_allowupload 1'.PHP_EOL
							.'sv_allowdownload 1';

					// Временый файл
					$temp = sys::temp($fastdl);

					$ssh->setfile($temp, '/servers/'.$server['uid'].'/cstrike/cfg/fastdl.cfg', 0644);

					$ssh->set('chown server'.$server['uid'].':servers /servers/'.$server['uid'].'/cstrike/cfg/fastdl.cfg;'
							.'ln -s /servers/'.$server['uid'].'/cstrike /var/nginx/fast_'.$server['uid'].';'
							.'sed -i '."'s/exec fastdl.cfg//g'".' /servers/'.$server['uid'].'/cstrike/cfg/server.cfg;'
							.'echo "exec fastdl.cfg" >> /servers/'.$server['uid'].'/cstrike/cfg/server.cfg');

					unlink($temp);
				}else
					$ssh->set('sed -i '."'s/exec fastdl.cfg//g'".' /servers/'.$server['uid'].'/cstrike/cfg/server.cfg;'
							.'rm /servers/'.$server['uid'].'/cstrike/cfg/fastdl.cfg; rm /var/nginx/fast_'.$server['uid']);

				$sql->query('UPDATE `control_servers` set `fastdl`="'.$value.'" WHERE `id`="'.$sid.'" LIMIT 1');

				$mcache->delete('ctrl_server_settings_'.$sid);
				sys::outjs(array('s' => 'ok'), $nmch);
		}
	}
	
	// Генерация списка слот
	$slots = '';

	for($slot = 2; $slot <= 64; $slot+=1)
		$slots .= '<option value="'.$slot.'">'.$slot.' шт.</option>';

	// Античит VAC
	$vac = $server['vac'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

	// Быстрая скачака
	$fastdl = $server['fastdl'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

	// Авторестарт при зависании
	$autorestart = $server['autorestart'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

	$fps = '';

	foreach(array('300', '500', '1100') as $value)
		$fps .= '<option value="'.$value.'">'.$value.' FPS</option>';

	$tickrate = '';

	foreach(array('33', '66', '100') as $value)
		$tickrate .= '<option value="'.$value.'">'.$value.' TickRate</option>';

	$core_fix = $server['core_fix'] ? '<option value="1">1 ядро/поток</option>' : '<option value="0">Автоматическое определение</option>';

	$html->get('start', 'sections/control/servers/'.$server['game'].'/settings');

		$html->set('id', $id);
		$html->set('server', $sid);
		$html->set('map', $server['map_start']);
		$html->set('address', $server['address']);
		$html->set('vac', $vac);
		$html->set('fastdl', $fastdl);
		$html->set('autorestart', $autorestart);
		$html->set('core_fix', $core_fix);
		$html->set('slots', str_replace('"'.$server['slots'].'"', '"'.$server['slots'].'" selected="select"', $slots));
		$html->set('tickrate', str_replace($server['tickrate'].'"', $server['tickrate'].'" selected="select"', $tickrate));
		$html->set('fps', str_replace($server['fps'].'"', $server['fps'].'" selected="select"', $fps));

	$html->pack('start');
?>