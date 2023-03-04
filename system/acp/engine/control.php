<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-dropbox"></i> Контроль';

	$aSection = array(
		'index',
		'overdue',
		'delete'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$del = $cfg['server_delete']*86400;
	$time = $start_point-$del;

	$html->get('menu', 'sections/control');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

		$sql->query('SELECT `id` FROM `control` WHERE `user`!="-1"');
		$html->set('all', $sql->num());

		$sql->query('SELECT `id` FROM `control` WHERE `user`!="-1" AND `time`<"'.$start_point.'" AND `overdue`>"'.$time.'"');
		$html->set('overdue', $sql->num());

	$html->pack('menu');

	include(SEC.'control/'.$section.'.php');
?>