<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-bullhorn"></i> Управление уведомлениями';

	$aSection = array(
		'index',
		'add',
		'end',
		'delete'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/notice');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

		$sql->query('SELECT `id` FROM `notice` WHERE `time`>"'.$start_point.'"');
		$html->set('active', $sql->num());

	$html->pack('menu');

	include(SEC.'notice/'.$section.'.php');
?>