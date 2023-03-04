<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($url['response']) AND !in_array($url['response'], array('success', 'fail')))
		exit();

	if($url['response'] == 'success')
		sys::out(file_get_contents(ROOT.'success.html'));
	else
		sys::out(file_get_contents(ROOT.'fail.html'));
?>