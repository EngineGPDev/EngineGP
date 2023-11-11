<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	$sql->query('SELECT `id` FROM `units` WHERE `id`="'.$server['unit'].'" AND `ddos`="1" LIMIT 1');
	if($sql->num())
		sys::outjs(array('e' => 'В данный момент нельзя изменить параметр, т.к. включена защита на всю локацию.'), $name_mcache);

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	if(!isset($url['type']) || !in_array($url['type'], array('0', '1', '2')))
		sys::outjs(array('e' => 'Неправильно передан параметр.'), $name_mcache);

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::outjs(array('e' => sys::text('error', 'ssh', $user['group'])), $name_mcache);

	list($ip, $port) = explode(':', $server['address']);

	$geo = $cfg['iptables'].'_geo';

	if($url['type'] == 2)
	{
		if($server['ddos'] == 2)
			sys::outjs(array('s' => 'ok'), $name_mcache);

		$cmd = '';

		if($server['ddos'])
			$cmd = 'iptables -D INPUT -p udp -d '.$ip.' --dport '.$port.' -m geoip ! --source-country UA,RU -j DROP;'
				.'sed "`nl '.$geo.' | grep \"#'.$id.'\" | awk \'{print $1","$1+1}\'`d" '.$geo.' > '.$geo.'_temp; cat '.$geo.'_temp > '.$geo.'; rm '.$geo.'_temp;';

		$rule = 'iptables -I INPUT -p udp -d '.$ip.' --dport '.$port.' -m geoip ! --source-country AM,BY,UA,RU,KZ -j DROP;';

		$ssh->set($cmd.$rule.' echo -e "#'.$id.';\n'.$rule.'" >> '.$geo);

		$sql->query('UPDATE `servers` set `ddos`="2" WHERE `id`="'.$id.'" LIMIT 1');
	}elseif($url['type'] == 1){
		if($server['ddos'] == 1)
			sys::outjs(array('s' => 'ok'), $name_mcache);

		$cmd = '';

		if($server['ddos'])
			$cmd = 'iptables -D INPUT -p udp -d '.$ip.' --dport '.$port.' -m geoip ! --source-country AM,BY,UA,RU,KZ -j DROP;'
				.'sed "`nl '.$geo.' | grep \"#'.$id.'\" | awk \'{print $1","$1+1}\'`d" '.$geo.' > '.$geo.'_temp; cat '.$geo.'_temp > '.$geo.'; rm '.$geo.'_temp;';

		$rule = 'iptables -I INPUT -p udp -d '.$ip.' --dport '.$port.' -m geoip ! --source-country UA,RU -j DROP;';

		$ssh->set($cmd.$rule.' echo -e "#'.$id.';\n'.$rule.'" >> '.$geo);

		$sql->query('UPDATE `servers` set `ddos`="1" WHERE `id`="'.$id.'" LIMIT 1');
	}else{
		if(!$server['ddos'])
			sys::outjs(array('s' => 'ok'), $name_mcache);

		$country = $server['ddos'] == 2 ? 'AM,BY,UA,RU,KZ' : 'UA,RU';

		$ssh->set('iptables -D INPUT -p udp -d '.$ip.' --dport '.$port.' -m geoip ! --source-country '.$country.' -j DROP;'
			.'sed "`nl '.$geo.' | grep \"#'.$id.'\" | awk \'{print $1","$1+1}\'`d" '.$geo.' > '.$geo.'_temp; cat '.$geo.'_temp > '.$geo.'; rm '.$geo.'_temp;');

		$sql->query('UPDATE `servers` set `ddos`="0" WHERE `id`="'.$id.'" LIMIT 1');
	}

	$mcache->delete('server_settings_'.$id);

	sys::outjs(array('s' => 'ok'), $name_mcache);
?>