<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aStyle = array(
		'default' => array(
			'fon' => array('R' => 232, 'G' => 235, 'B' => 240),
			'border' => array('R' => 220, 'G' => 220, 'B' => 220),
			'graph' => array('R' => 255, 'G' => 255, 'B' => 255, 'Surrounding' => -200, 'Alpha' => 10),
			'line' => array('R' => 68, 'G' => 148, 'B' => 224),
			'leftbox' => array('R' => 0, 'G' => 0, 'B' => 0),
			'box' => array('R' => 0, 'G' => 0, 'B' => 0),
			'boxcolor' => array('R' => 255, 'G' => 255, 'B' => 255),
			'color' => array('R' => 0, 'G' => 0, 'B' => 0),
			'progress' => array('R' => 68, 'G' => 148, 'B' => 224)
		),

		'black' => array(
			'fon' => array('R' => 0, 'G' => 0, 'B' => 0),
			'border' => array('R' => 232, 'G' => 235, 'B' => 240),
			'graph' => array('R' => 232, 'G' => 235, 'B' => 240, 'Surrounding' => -200, 'Alpha' => 100),
			'line' => array('R' => 68, 'G' => 148, 'B' => 224),
			'leftbox' => array('R' => 255, 'G' => 255, 'B' => 255),
			'box' => array('R' => 255, 'G' => 255, 'B' => 255),
			'boxcolor' => array('R' => 0, 'G' => 0, 'B' => 0),
			'color' => array('R' => 255, 'G' => 255, 'B' => 255),
			'progress' => array('R' => 68, 'G' => 148, 'B' => 224)
		),

		'camo' => array(
			'fon' => array('R' => 55, 'G' => 62, 'B' => 40),
			'border' => array('R' => 62, 'G' => 68, 'B' => 51),
			'graph' => array('R' => 46, 'G' => 50, 'B' => 37, 'Surrounding' => -200, 'Alpha' => 10),
			'line' => array('R' => 166, 'G' => 186, 'B' => 149),
			'leftbox' => array('R' => 32, 'G' => 35, 'B' => 27),
			'box' => array('R' => 46, 'G' => 50, 'B' => 37),
			'boxcolor' => array('R' => 210, 'G' => 225, 'B' => 181),
			'color' => array('R' => 136, 'G' => 156, 'B' => 99),
			'progress' => array('R' => 136, 'G' => 156, 'B' => 99, 'BoxBorderR' => 46, 'BoxBorderG' => 50, 'BoxBorderB' => 37)
		),
	);
?>