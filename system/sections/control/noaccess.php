<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Раздел недоступен');

	if($ctrl['time'] < $start_point)
		$html->get('overdue');
	else{
		$status = array(
			'install' => 'установки',
			'reboot' => 'перезагрузки',
			'blocked' => 'блокировки'
		);

		$html->get('noaccess');

			$html->set('status', $status[$ctrl['status']]);
	}

	$html->pack('main');
?>