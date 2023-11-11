<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($ctrl['status'] != 'working')
		sys::outjs(array('e' => 'Сервер должен быть в рабочем состоянии'));

	$sql->query('SELECT `address`, `passwd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
	$ctrl = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($ctrl['passwd'], $ctrl['address']))
		sys::outjs(array('e' => 'Неудалось создать связь с физическим сервером'));

	$ssh->set('screen -dmS reboot reboot');

	$sql->query('UPDATE `control` set `status`="reboot" WHERE `id`="'.$id.'" LIMIT 1');
	$sql->query('UPDATE `control_servers` set `status`="off" WHERE `unit`="'.$id.'" LIMIT 1');

	$mcache->set('reboot_control_'.$id, true, false, 10);

	sys::outjs(array('s' => 'ok'));
?>