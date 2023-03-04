<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT `game`, `status` FROM `control_servers` WHERE `id`="'.$sid.'" LIMIT 1');
	$server = $sql->get();

	if(!isset($url['action']))
		sys::outjs(array('e' => 'Неверный запрос для выполнения операции'));

	$nmch = 'ctrl_server_action_'.$sid;

	if($mcache->get($nmch))
		sys::outjs(array('e' => sys::text('other', 'mcache')));

	$mcache->set($nmch, true, false, 10);

	include(LIB.'control/'.$server['game'].'/action.php');
	
	switch($url['action'])
	{
		case 'stop':
			if(!in_array($server['status'], array('working', 'start', 'restart', 'change')))
				sys::outjs(array('e' => sys::text('error', 'ser_stop')), $nmch);

			sys::outjs(action::stop($sid), $nmch);

		case 'start':
			if($server['status'] != 'off')
				sys::outjs(array('e' => sys::text('error', 'ser_start')), $nmch);

			sys::outjs(action::start($sid), $nmch);

		case 'restart':
			if(!in_array($server['status'], array('working', 'start', 'restart', 'change')))
				sys::outjs(array('e' => sys::text('error', 'ser_restart')), $nmch);

			sys::outjs(action::start($sid, 'restart'), $nmch);

		case 'change':
			if($server['status'] != 'working')
			{
				if($server['status'] == 'change')
					sys::outjs(array('e' => sys::text('other', 'mcache')), $nmch);
				
				sys::outjs(array('e' => sys::text('error', 'ser_change')), $nmch);
			}

			if(isset($url['change']))
				sys::outjs(action::change($sid, $url['change']), $nmch);

			sys::outjs(action::change($sid), $nmch);

		case 'reinstall':
			if($server['status'] != 'off')
				sys::outjs(array('e' => sys::text('error', 'ser_reinstall')), $nmch);

			sys::outjs(action::reinstall($sid), $nmch);

		case 'update':
			if($server['status'] != 'off')
				sys::outjs(array('e' => sys::text('error', 'ser_update')), $nmch);

			sys::outjs(action::update($sid), $nmch);

		case 'delete':
			if($server['status'] != 'off')
				sys::outjs(array('e' => sys::text('error', 'ser_delete')), $nmch);

			sys::outjs(action::delete($sid), $nmch);
	}

	exit;
?>