<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-cubes"></i> Управление википедией';

	$aSection = array(
		'addcat',
		'cat',
		'cats',
		'index',
		'add',
		'delete'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/wiki');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

	$html->pack('menu');

	include(SEC.'wiki/'.$section.'.php');
?>