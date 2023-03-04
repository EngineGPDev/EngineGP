<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-server"></i> Управление локациями';

	$aSection = array(
		'index',
		'add',
		'loading',
		'stats',
		'delete'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/units');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

		$sql->query('SELECT `id` FROM `units`');
		$html->set('units', $sql->num());

	$html->pack('menu');

	include(SEC.'units/'.$section.'.php');
?>