<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Список подключенных серверов', $cfg['http'].'control');

	if(in_array($ctrl['status'], array('install', 'overdue', 'blocked')))
		include(SEC.'control/noaccess.php');
	else{
		$sql->query('SELECT `address`, `passwd`, `fcpu`, `ram`, `hdd` FROM `control` WHERE `id`="'.$id.'" LIMIT 1');
		$ctrl = $sql->get();

		if($go)
		{
			$aData = array();

			$aData['passwd'] = isset($_POST['passwd']) ? trim($_POST['passwd']) : $ctrl['passwd'];
			$aData['fcpu'] = isset($_POST['fcpu']) ? sys::int($_POST['fcpu']) : $ctrl['fcpu'];
			$aData['ram'] = isset($_POST['ram']) ? sys::int($_POST['ram']) : $ctrl['ram'];
			$aData['hdd'] = isset($_POST['hdd']) ? sys::int($_POST['hdd']) : $ctrl['hdd'];

			include(LIB.'ssh.php');

			if(sys::strlen($aData['passwd']) > 32)
			sys::outjs(array('e' => 'Указанный пароль слишком длинный'));

			if(sys::valid($aData['passwd'], 'other', $aValid['passwd']))
				sys::outjs(array('e' => 'Пожалуйста, поменяйте пароль используя только латинские буквы и цифры'));

			if(!$ssh->auth($aData['passwd'], $ctrl['address']))
				sys::outjs(array('e' => 'Неудалось создать связь с физическим сервером'));

			$aData['fcpu'] = $aData['fcpu'] == 1 ? 1 : 0;

			if($aData['ram'] < 1 || $aData['ram'] > 9999999)
				$aData['ram'] = 0;

			if($aData['hdd'] < 1 || $aData['hdd'] > 9999999)
				$aData['hdd'] = 0;

			$sql->query('UPDATE `control` set `passwd`="'.$aData['passwd'].'", `fcpu`="'.$aData['fcpu'].'", `ram`="'.$aData['ram'].'", `hdd`="'.$aData['hdd'].'" WHERE `id`="'.$id.'" LIMIT 1');

			sys::outjs(array('s' => 'ok'));
		}

		$html->nav('Параметры сервера #'.$id);

		$html->get('settings', 'sections/control');
			$html->set('id', $id);
			$html->set('passwd', $ctrl['passwd']);
			$html->set('ram', $ctrl['ram']);
			$html->set('hdd', $ctrl['hdd']);
			$html->set('fcpu', $ctrl['fcpu'] ? '<option value="1">Активный</option><option value="0">Пассивный</option>' : '<option value="0">Пассивный</option><option value="1">Активный</option>');
		$html->pack('main');
	}
?>