<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$list = '';

	$all_money = 0;
	$all_money_month = 0;

	$tarifs = $sql->query('SELECT `id`, `unit`, `game`, `name` FROM `tarifs` ORDER BY `id` ASC');
	while($tarif = $sql->get($tarifs))
	{
		$servers = $sql->query('SELECT `id`, `user` FROM `servers` WHERE `tarif`="'.$tarif['id'].'"');
		$all_servers = $sql->num($servers);

		$money_all = 0;
		$money_month = 0;

		$time = date('j', $start_point)*86400;

		while($server = $sql->get($servers))
		{
			$sql->query('SELECT `id` FROM `users` WHERE `id`="'.$server['user'].'" AND `group`="user" LIMIT 1');
			if(!$sql->num())
				continue;

			$sql->query('SELECT `money`, `date` FROM `logs` WHERE `user`="'.$server['user'].'" AND (`type`="buy" OR `type`="extend") AND `text` LIKE \'%(сервер: #'.$server['id'].')%\'');
			while($logs = $sql->get())
			{
				$money_all += $logs['money'];

				if($logs['date'] >= ($start_point-$time))
					$money_month += $logs['money'];
			}
		}

		if($money_all < 1)
			continue;

		$all_money += $money_all;
		$all_money_month += $money_month;

		$sql->query('SELECT `id` FROM `servers` WHERE `tarif`="'.$tarif['id'].'" AND `time`<"'.$start_point.'"');
		$overdue_servers = $sql->num();

		$sql->query('SELECT `name` FROM `units` WHERE `id`="'.$tarif['unit'].'" LIMIT 1');
		$unit = $sql->get();

		$list .= '<tr>';
			$list .= '<td>'.$tarif['id'].'</td>';
			$list .= '<td><a href="'.$cfg['http'].'acp/tarifs/id/'.$tarif['id'].'">'.$tarif['name'].'</a></td>';
			$list .= '<td>'.$unit['name'].'</td>';
			$list .= '<td class="text-center">'.$all_servers.' шт./'.$overdue_servers.' шт.</td>';
			$list .= '<td class="text-center">'.$money_all.' '.$cfg['currency'].'</td>';
			$list .= '<td class="text-center">'.$money_month.' '.$cfg['currency'].'</td>';
			$list .= '<td class="text-center">'.strtoupper($tarif['game']).'</td>';
		$list .= '</tr>';
	}

	$html->get('stats', 'sections/tarifs');

		$html->set('list', $list);

		$html->set('month', mb_strtolower(params::$aNameMonth[sys::int(date('n', $start_point))], 'UTF-8'));

		$html->set('all_money', $all_money);
		$html->set('all_money_month', $all_money_month);
		$html->set('cur', $cfg['currency']);

	$html->pack('main');
?>