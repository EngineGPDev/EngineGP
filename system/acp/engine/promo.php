<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-gift"></i> Управление акциями';

	$aSection = array(
		'index',
		'add',
		'end',
		'stats',
		'delete'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/promo');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

		$sql->query('SELECT `id` FROM `promo` WHERE `time`>"'.$start_point.'"');
		$html->set('active', $sql->num());

		$sql->query('SELECT `id` FROM `promo` WHERE `time`<"'.$start_point.'"');
		$html->set('end', $sql->num());

	$html->pack('menu');

	include(SEC.'promo/'.$section.'.php');
?>