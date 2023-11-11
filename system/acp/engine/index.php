<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$mcache_name = 'acp_main';

	$html->arr['main'] = $mcache->get($mcache_name);

	$cbs = $sql->query('SELECT * FROM `cashback` WHERE `status`="1" ORDER BY `date` ASC');
	while($cb = $sql->get($cbs))
	{
		$sql->query('SELECT `mail` FROM `users` WHERE `id`="'.$cb['user'].'" LIMIT 1');
		$us = $sql->get();

		$html->get('cashback');

			$html->set('id', $cb['id']);
			$html->set('user', $cb['user']);
			$html->set('mail', $us['mail']);
			$html->set('money', $cb['money'].' '.$cfg['currency']);
			$html->set('cashback', ($cb['money']-($cb['money']/100*$cfg['part_output_proc'])).' '.$cfg['currency']);
			$html->set('type', $cb['purse']{0} == 'R' ? '<span class="text-blue">WebMoney</span>' : '<span class="text-orange">Qiwi</span>');
			$html->set('purse', $cb['purse']);
			$html->set('gateway', empty($cfg['part_gateway']) ? 'mm' : 'auto');
			$html->set('date', sys::today($cb['date']));

		$html->pack('cashback');
	}

	$html->arr['cashback'] = isset($html->arr['cashback']) ? $html->arr['cashback'] : '';

	if($html->arr['main'] == '')
	{
		$sql->query('SELECT `id` FROM `users`');
		$users = $sql->num();

		$sql->query('SELECT `id`, `game`, `slots` FROM `servers`');
		$servers = $sql->num();

		$aSlots = array('cs' => 0, 'cssold' => 0, 'css' => 0, 'csgo' => 0, 'samp' => 0, 'crmp' => 0, 'mta' => 0, 'mc' => 0);
		$aServers = array('cs' => 0, 'cssold' => 0, 'css' => 0, 'csgo' => 0, 'samp' => 0, 'crmp' => 0, 'mta' => 0, 'mc' => 0);

		while($server = $sql->get())
		{
			$aSlots[$server['game']] += $server['slots'];
			$aServers[$server['game']] += 1;
		}

		$sql->query('SELECT SUM(`money`) FROM `logs` WHERE `type`="replenish"');
		$replenish = $sql->get();

		$sf_list = '';

		$sql->query('SELECT `id`, `name`, `group`, `lastname`, `ip`, `browser`, `time` FROM `users` WHERE `group`!="user" ORDER BY `id` ASC LIMIT 20');
		while($staff = $sql->get())
		{
			$online = $staff['time']+15 > $start_point ? 'text-green">Онлайн' : 'text-red">Офлайн';

			$group = $staff['group'] == 'admin' ? 'text-red">Администратор' : '">Тех. Поддержка';

			$sf_list .= '<tr>';
				$sf_list .= '<td>'.$staff['id'].'</td>';
				$sf_list .= '<td><a href="'.$cfg['http'].'acp/users/id/'.$staff['id'].'">'.$staff['lastname'].' '.$staff['name'].'</a></td>';
				$sf_list .= '<td class="text-center '.$group.'</td>';
				$sf_list .= '<td class="text-center">'.$staff['ip'].'</td>';
				$sf_list .= '<td class="text-center">'.$staff['browser'].'</td>';
				$sf_list .= '<td class="text-center '.$online.'</td>';
				$sf_list .= '<td class="text-right">'.sys::today($staff['time']).'</td>';
			$sf_list .= '</tr>';
		}

		$html->get('main');

			$html->set('cashback', $html->arr['cashback']);
			$html->set('users', $users);
			$html->set('servers', $servers);
			$html->set('replenish', $replenish['SUM(`money`)']);
			$html->set('staff', $sf_list);

			foreach($aSlots as $game => $slots)
				$html->set('slots_'.$game, $slots);

			foreach($aServers as $game => $num)
				$html->set($game, $num);

		$html->pack('main');

		$mcache->set($mcache_name, $html->arr['main'], false, 10);
	}else
		$html->arr['main'] = str_replace('[cashback]', $html->arr['cashback'], $html->arr['main']);
?>