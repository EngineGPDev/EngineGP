<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(isset($url['subsection']) AND $url['subsection'] == 'search')
		include(SEC.'boost/sysearch.php');

	$list = '';

	$sql->query('SELECT `id` FROM `boost` WHERE `site`="'.$section.'"');

	$aPage = sys::page($page, $sql->num(), 40);

	sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'acp/boost/section/'.$section);

	$sql->query('SELECT * FROM `boost` WHERE `site`="'.$section.'" ORDER BY `id` DESC LIMIT '.$aPage['num'].', 40');
	while($log = $sql->get())
	{
		$list .= '<tr>';
			$list .= '<td>'.$log['id'].'</td>';
			$list .= '<td>Покупка кругов: '.$log['circles'].' шт. на сайте: '.$aBoost['cs'][$log['site']]['site'].', списана сумма: '.$log['money'].' '.$cfg['currency'].'</td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/users/id/'.$log['user'].'">USER_'.$log['user'].'</a></td>';
			$list .= '<td class="text-center"><a href="'.$cfg['http'].'acp/servers/id/'.$log['server'].'">SERVER_'.$log['server'].'</a></td>';
			$list .= '<td class="text-center">'.date('d.m.Y - H:i:s', $log['date']).'</td>';
		$list .= '</tr>';
	}

	$month = date('n', $start_point)-1;

	if(!$month)
		$month = 12;

	$aData = $mcache->get('data_boost_'.$section);

	if(!is_array($aData))
	{
		$sql->query('SELECT SUM(`circles`), SUM(`money`) FROM `boost` WHERE `site`="'.$section.'"');
		$data = $sql->get();

		$aData['all_num'] = (int) $data['SUM(`circles`)'];
		$aData['all_sum'] = (int) $data['SUM(`money`)'];

		$now = $start_point-(date('d', $start_point)*86400);

		$old = $start_point-(params::$aDayMonth[$month]*86400);

		$sql->query('SELECT SUM(`circles`), SUM(`money`) FROM `boost` WHERE `site`="'.$section.'" AND `date`>="'.$old.'" AND date<"'.$now.'"');
		$data = $sql->get();

		$aData['old_num'] = (int) $data['SUM(`circles`)'];
		$aData['old_sum'] = (int) $data['SUM(`money`)'];

		$sql->query('SELECT SUM(`circles`), SUM(`money`) FROM `boost` WHERE `site`="'.$section.'" AND `date`>="'.$now.'"');
		$data = $sql->get();

		$aData['now_num'] = (int) $data['SUM(`circles`)'];
		$aData['now_sum'] = (int) $data['SUM(`money`)'];

		$mcache->set('data_boost_'.$section, $aData, false, 60);
	}

	$html->get('index', 'sections/boost');

		$html->set('list', $list);

		$html->set('month_old', params::$aNameMonth[$month]);
		$html->set('month_now', params::$aNameMonth[date('n', $start_point)]);

		$html->set('all_num', $aData['all_num']);
		$html->set('all_sum', $aData['all_sum']);
		$html->set('old_num', $aData['old_num']);
		$html->set('old_sum', $aData['old_sum']);
		$html->set('now_num', $aData['now_num']);
		$html->set('now_sum', $aData['now_sum']);

		$html->set('cur', $cfg['currency']);

		$html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');

	$html->pack('main');
?>