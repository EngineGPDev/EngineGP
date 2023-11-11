<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$sql->query('SELECT `address`, `passwd` FROM `panel` LIMIT 1');
		$unit = $sql->get();

		include(LIB.'ssh.php');

		if(isset($url['service']) AND in_array($url['service'], array('apache2', 'nginx', 'mysql', 'unit')))
		{
			if(!$ssh->auth($unit['passwd'], $unit['address']))
				sys::outjs(array('e' => 'Не удалось создать связь с сервером'));

			if($url['service'] == 'unit')
				$ssh->set('screen -dmS reboot reboot');
			else
				$ssh->set('screen -dmS sr_'.$url['service'].' service '.$url['service'].' restart');

			sys::outjs(array('s' => 'ok'));
		}

		$aData = array(
			'cpu' => '0%',
			'ram' => '0%',
			'hdd' => '0%',
			'apache' => '<a href="#" onclick="return system_restart(\'apache\')">Перезагрузить</a>',
			'nginx' => '<a href="#" onclick="return system_restart(\'nginx\')">Перезагрузить</a>',
			'mysql' => '<a href="#" onclick="return system_restart(\'mysql\')">Перезагрузить</a>',
			'uptime' => 'unknown',
			'ssh' => 'error'
		);

		if(!$ssh->auth($unit['passwd'], $unit['address']))
			sys::outjs($aData);

		$aData['ssh'] = '<i class="fa fa-retweet pointer" id="system_restart(\'unit\')" onclick="return system_restart(\'unit\')"></i>';

		$stat_ram = $ssh->get('echo `cat /proc/meminfo | grep MemTotal | awk \'{print $2}\'; cat /proc/meminfo | grep MemFree | awk \'{print $2}\'; cat /proc/meminfo | grep Buffers | awk \'{print $2}\'; cat /proc/meminfo | grep Cached | grep -v SwapCached | awk \'{print $2}\'`');
		$aData['ram'] = ceil(sys::ram_load($stat_ram)).'%';

		$aData['hdd'] = $ssh->get('df -P / | awk \'{print $5}\' | tail -1');

		$time = ceil($ssh->get('cat /proc/uptime | awk \'{print $1}\''));
		$aData['uptime'] = sys::uptime_load($time);

		$aData['cpu'] = sys::cpu_load($ssh->get('echo "`ps -A -o pcpu | tail -n+2 | paste -sd+ | bc | awk \'{print $0}\'` `cat /proc/cpuinfo | grep processor | wc -l | awk \'{print $1}\'`"')).'%';

		sys::outjs($aData);
	}

	$html->get('index', 'sections/system');

	$html->pack('main');
?>