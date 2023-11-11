<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	// Выполнение операции
	if($go)
	{
		if($server['status'] != 'off')
			sys::outjs(array('e' => 'Игровой сервер должен быть выключен'), $nmch);

		$pack = isset($url['pack']) ? $url['pack'] : sys::outjs(array('e' => 'Переданы не все данные.'), $nmch);

		// Проверка сборки
		if(!array_key_exists($pack, sys::b64djs($tarif['packs'], true)))
			sys::outjs(array('e' => 'Сборка не найдена.'));

		$sql->query('SELECT `id`, `unit`, `port_min`, `port_max`, `hostname`, `path`, `install`, `map`, `plugins_install`, `hdd`, `autostop`, `core_fix`, `ip` FROM `tarifs` WHERE `id`="'.$tarif['id'].'" LIMIT 1');
		$tarif = array_merge(array('pack' => $pack), $sql->get());

		$sql->query('SELECT `name`, `address`, `passwd` FROM `units` WHERE `id`="'.$tarif['unit'].'" LIMIT 1');
		$unit = $sql->get();

		// Выделенный адрес игрового сервера
		if(!empty($tarif['ip']))
		{
			$aIp = explode(':', $tarif['ip']);

			$ip = false;
			$port = params::$aDefPort[$server['game']];

			// Проверка наличия свободного адреса
			foreach($aIp as $adr)
			{
				$adr = trim($adr);

				$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$tarif['unit'].'" AND `address` LIKE "'.$adr.':%" LIMIT 1');
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
				$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$tarif['unit'].'" AND `port`="'.$tarif['port_min'].'" LIMIT 1');
				if(!$sql->num())
				{
					$port = $tarif['port_min'];

					break;
				}
			}
		}

		$core = 0;

		if($tarif['core_fix'] != '')
		{
			$aCore = explode(',', $tarif['core_fix']);

			foreach($aCore as $cpu)
			{
				$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$tarif['unit'].'" AND `tarif`="'.$tarif['id'].'" AND `core_fix`="'.$cpu.'" AND `core_fix_one`="1" LIMIT 1');

				if($sql->num())
					continue;

				$core = $cpu;
				$tarif['core_fix'] = $cpu;

				break;
			}
		}

		if(!$ip || !$port || !$core)
			sys::outjs(array('e' => 'К сожалению нет доступных мест, обратитесь в тех.поддержку.'));

		$server['id'] = $id;

		// Време аренды
		$tarif['time'] = $time;

		include(LIB.'ssh.php');

		// Удаление данных с текущей локации
		tarif::unit_old($oldTarif, $oldUnit, $server, $nmch);

		$mcache->delete('server_filetp_'.$id);

		$adUnit = explode(':', $unit['address']);

		$server['address'] = $ip.':'.$port;

		// Создание сервера на новой локации
		tarif::unit_new($tarif, $unit, $server, $nmch);

		// Запись логов
		$sql->query('INSERT INTO `logs_sys` set `user`="'.$user['id'].'", `server`="'.$id.'", `text`="'.sys::text('syslogs', 'change_unit').'", `time`="'.$start_point.'"');

		sys::outjs(array('s' => 'ok'), $nmch);
	}

	// Генерация списка сборок
	$packs = '';
	$aPack = sys::b64djs($tarif['packs'], true);

	if(is_array($aPack))
		foreach($aPack as $index => $name)
			$packs .= '<option value="'.$index.'">'.$name.'</option>';

	// Выхлоп информации
	sys::outjs(array('s' => date('d.m.Y - H:i', $time).' ('.sys::date('min', $time).')', 'p' => $packs));
?>