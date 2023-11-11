<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `uid`, `slots`, `slots_start`, `map_start`, `vac`, `fastdl`, `autorestart`, `fps`, `tickrate` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
	$server = array_merge($server, $sql->get());
	
	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	$sql->query('SELECT `install`, `tickrate`, `price` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	include(LIB.'games/games.php');
	include(LIB.'games/tarifs.php');
	include(LIB.'games/'.$server['game'].'/tarif.php');

	// Вывод списка карт
	if(isset($url['maps']))
		games::maplist($id, $unit, $tarif['install'].$server['uid'].'/csgo/maps', $server['map_start'], false);

	// Сохранение
	if($go AND $url['save'])
	{
		$value = isset($url['value']) ? sys::int($url['value']) : sys::outjs(array('s' => 'ok'), $nmch);
		
		switch($url['save'])
		{
			case 'map':
				$map = isset($url['value']) ? trim($url['value']) : sys::outjs(array('s' => 'ok'), $nmch);

				if($map != $server['map_start'])
					games::maplist($id, $unit, $tarif['install'].$server['uid'].'/csgo/maps', $map, true, $nmch);

				sys::outjs(array('s' => 'ok'), $nmch);

			case 'vac':
				if($value != $server['vac'])
					$sql->query('UPDATE `servers` set `vac`="'.$value.'" WHERE `id`="'.$id.'" LIMIT 1');
				
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'slots':
				$slots = $value > $server['slots'] ? $server['slots'] : $value;
				$slots = $value < 2 ? 2 : $slots;

				if($slots != $server['slots_start'])
					$sql->query('UPDATE `servers` set `slots_start`="'.$slots.'" WHERE `id`="'.$id.'" LIMIT 1');
				
				sys::outjs(array('s' => 'ok'), $nmch);

			case 'autorestart':
				if($value != $server['autorestart'])
					$sql->query('UPDATE `servers` set `autorestart`="'.$value.'" WHERE `id`="'.$id.'" LIMIT 1');

				sys::outjs(array('s' => 'ok'), $nmch);

			case 'tickrate':
				if(!tarif::price($tarif['price']) AND in_array($value, explode(':', $tarif['tickrate'])))
					$sql->query('UPDATE `servers` set `tickrate`="'.$value.'" WHERE `id`="'.$id.'" LIMIT 1');

				sys::outjs(array('s' => 'ok'), $nmch);

			case 'fastdl':
				include(LIB.'ssh.php');

				if(!$ssh->auth($unit['passwd'], $unit['address']))
					sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

				if($value)
				{
					$fastdl = 'sv_downloadurl "http://'.sys::first(explode(':', $unit['address'])).':8080/fast_'.$server['uid'].'"'.PHP_EOL
							.'sv_consistency 1'.PHP_EOL
							.'sv_allowupload 1'.PHP_EOL
							.'sv_allowdownload 1';

					// Временый файл
					$temp = sys::temp($fastdl);

					$ssh->setfile($temp, $tarif['install'].$server['uid'].'/csgo/cfg/fastdl.cfg', 0644);
						
					$ssh->set('chown server'.$server['uid'].':servers '.$tarif['install'].$server['uid'].'/csgo/cfg/fastdl.cfg;'
							.'ln -s '.$tarif['install'].$server['uid'].'/csgo /var/nginx/fast_'.$server['uid'].';'
							.'sed -i '."'s/exec fastdl.cfg//g'".' '.$tarif['install'].$server['uid'].'/csgo/cfg/server.cfg;'
							.'echo "exec fastdl.cfg" >> '.$tarif['install'].$server['uid'].'/csgo/cfg/server.cfg');

					unlink($temp);
				}else
					$ssh->set('sed -i '."'s/exec fastdl.cfg//g'".' '.$tarif['install'].$server['uid'].'/csgo/cfg/server.cfg;'
							.'rm '.$tarif['install'].$server['uid'].'/csgo/cfg/fastdl.cfg; rm /var/nginx/fast_'.$server['uid']);

				$sql->query('UPDATE `servers` set `fastdl`="'.$value.'" WHERE `id`="'.$id.'" LIMIT 1');

				sys::outjs(array('s' => 'ok'), $nmch);
		}
	}
	
	// Генерация списка слот
	$slots = '';

	for($slot = 2; $slot <= $server['slots']; $slot+=1)
		$slots .= '<option value="'.$slot.'">'.$slot.' шт.</option>';

	// Античит VAC
	$vac = $server['vac'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

	// Быстрая скачака
	$fastdl = $server['fastdl'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

	// Авторестарт при зависании
	$autorestart = $server['autorestart'] ? '<option value="1">Включен</option><option value="0">Выключен</option>' : '<option value="0">Выключен</option><option value="1">Включен</option>';

	$tickrate = '<option value="'.$server['tickrate'].'">'.$server['tickrate'].' TickRate</option>';

	if(!tarif::price($tarif['price']))
	{
		$aTick = explode(':', $tarif['tickrate']);

		unset($aTick[array_search($server['tickrate'], $aTick)]);

		if(count($aTick))
			foreach($aTick as $value)
				$tickrate .= '<option value="'.$value.'">'.$value.' TickRate</option>';
	}

	$html->get('start', 'sections/servers/'.$server['game'].'/settings');

		$html->set('id', $id);
		$html->set('map', $server['map_start']);
		$html->set('vac', $vac);
		$html->set('fastdl', $fastdl);
		$html->set('autorestart', $autorestart);
		$html->set('slots', str_replace('"'.$server['slots_start'].'"', '"'.$server['slots_start'].'" selected="select"', $slots));

		if(!tarif::price($tarif['price']))
		{
			$html->unit('tickrate', true);
			$html->set('tickrate', $tickrate);
		}else
			$html->unit('tickrate');

	$html->pack('main');
?>