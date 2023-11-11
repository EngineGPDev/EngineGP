<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-clipboard"></i> Логи операций';

	$aSection = array(
		'index',
		'buy',
		'extend',
		'boost',
		'cashout',
		'part',
		'search',
		'replenish'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/logs');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

	$html->pack('menu');

	include(SEC.'logs/'.$section.'.php');
?>