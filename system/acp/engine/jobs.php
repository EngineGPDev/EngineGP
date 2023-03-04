<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$info = '<i class="fa fa-envelope-open"></i> Вакансии';

	$aSection = array(
		'index',
		'add',
		'request'
	);

	if(!in_array($section, $aSection))
		$section = 'index';

	$html->get('menu', 'sections/jobs');

		$html->unit('s_'.$section, true);

		unset($aSection[array_search($section, $aSection)]);

		foreach($aSection as $noactive)
			$html->unit('s_'.$noactive);

		$sql->query('SELECT `id` FROM `jobs`');
		$html->set('jobs', $sql->num());
		$sql->query('SELECT `id` FROM `jobs_app`');
		$html->set('jobs_app', $sql->num());
	$html->pack('menu');

	include(SEC.'jobs/'.$section.'.php');
?>