<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

    $html->nav('Бан листы');

	$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="'.$server['unit'].'" LIMIT 1');
	$unit = $sql->get();

	$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="'.$server['tarif'].'" LIMIT 1');
	$tarif = $sql->get();

	include(LIB.'ssh.php');

	if(!$ssh->auth($unit['passwd'], $unit['address']))
		sys::back($cfg['http'].'servers/id/'.$id.'/section/settings');

	// Путь к файлам (banned_user.cfg / banned_ip.cfg)
	$folder = $tarif['install'].$server['uid'].'/cstrike';

	// Если бан/разбан/проверка
	if($go)
	{
		$aData = array();
		
		$aData['value'] = isset($_POST['value']) ? trim($_POST['value']) : sys::outjs(array('e' => sys::text('servers', 'bans')), $nmch);
		$aData['userid'] = isset($_POST['userid']) ? sys::int($_POST['userid']) : false;
		$aData['amxbans'] = isset($_POST['amxbans']) ? true : false;

		// Проверка входных данных
		if(sys::valid($aData['value'], 'steamid') AND sys::valid($aData['value'], 'steamid3') AND sys::valid($aData['value'], 'ip'))
			sys::outjs(array('e' => sys::text('servers', 'bans')), $nmch);

		// Если указан steamid
		if(sys::valid($aData['value'], 'ip'))
		{
			// бан
			if(isset($url['action']) AND $url['action'] == 'ban')
			{
				// Если включен sourcebans
				if($aData['amxbans'] AND $aData['userid'])
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"sm_ban 0 ".$aData['userid']." EGP\"\015'");
				else
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"banid 0.0 ".$aData['value']." kick\"\015'");

				$ssh->set('cd '.$folder.' && sudo -u server'.$server['uid'].' fgrep '.$aData['value'].' banned_user.cfg | awk \'{print $3}\'');

				if($aData['value'] != trim($ssh->get()))
					$ssh->set('sudo -u server'.$server['uid'].' sh -c "echo \"banid 0.0 '.$aData['value'].'\" >> '.$folder.'/banned_user.cfg"');

				sys::outjs(array('s' => 'ok'), $nmch);

			// разбан	
			}elseif(isset($url['action']) AND $url['action'] == 'unban'){
				// Убираем запись из banned_user.cfg
				$ssh->set('cd '.$folder.' && sudo -u server'.$server['uid'].' sh -c "cat banned_user.cfg | grep -v '.$aData['value'].' > temp_banned.cfg; echo "" >> temp_banned.cfg && cat temp_banned.cfg > banned_user.cfg; rm temp_banned.cfg"');

				// Если включен sourcebans
				if($aData['amxbans'])
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"sm_unban ".$aData['value']."\"\015'");
				else{
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"removeid ".$aData['value']."\"\015'");
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"writeid\"\015'");
				}

				sys::outjs(array('s' => 'ok'), $nmch);
			// проверка
			}else{
				$ssh->set('cd '.$folder.' && sudo -u server'.$server['uid'].' fgrep '.$aData['value'].' banned_user.cfg | awk \'{print $3}\'');

				if($aData['value'] == trim($ssh->get()))
					sys::outjs(array('ban' => 'Данный SteamID <u>найден</u> в файле banned_user.cfg'), $nmch);

				sys::outjs(array('unban' => 'Данный SteamID <u>не найден</u> в файле banned_user.cfg'), $nmch);
			}
		}else{
			// бан
			if(isset($url['action']) AND $url['action'] == 'ban')
			{
				// Если включен sourcebans
				if($aData['amxbans'])
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"sm_ban 0 ".$aData['value']." EGP\"\015'");
				else
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"addip 0.0 ".$aData['value']." EGP\"\015'");

				$ssh->set('cd '.$folder.' && sudo -u server'.$server['uid'].' fgrep '.$aData['value'].' banned_ip.cfg | awk \'{print $3}\'');

				if($aData['value'] != trim($ssh->get()))
					$ssh->set('sudo -u server'.$server['uid'].' sh -c "echo \"addip 0.0 '.$aData['value'].'\" >> '.$folder.'/banned_ip.cfg"');

				sys::outjs(array('s' => 'ok'), $nmch);

			// разбан	
			}elseif(isset($url['action']) AND $url['action'] == 'unban'){
				// Убираем запись из banned_ip.cfg
				$ssh->set('cd '.$folder.' && sudo -u server'.$server['uid'].' sh -c "cat banned_ip.cfg | grep -v '.$aData['value'].' > temp_listip.cfg; echo "" >> temp_listip.cfg && cat temp_listip.cfg > banned_ip.cfg; rm temp_listip.cfg"');

				// Если включен sourcebans
				if($aData['amxbans'])
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"amx_unban ".$aData['value']."\"\015'");
				else{
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"removeip ".$aData['value']."\"\015'");
					$ssh->set("sudo -u server".$server['uid']." screen -p 0 -S s_".$server['uid']." -X eval 'stuff \"writeip\"\015'");
				}

				sys::outjs(array('s' => 'ok'), $nmch);
			// проверка
			}else{
				$ssh->set('cd '.$folder.' && sudo -u server'.$server['uid'].' fgrep '.$aData['value'].' banned_ip.cfg | awk \'{print $3}\'');

				if($aData['value'] == trim($ssh->get()))
					sys::outjs(array('ban' => 'Данный IP <u>найден</u> в файле banned_ip.cfg'), $nmch);

				sys::outjs(array('unban' => 'Данный IP <u>не найден</u> в файле banned_ip.cfg'), $nmch);
			}
		}
	}

	// Содержимое banned_user.cfg
	$ssh->set('cd '.$folder.' && cat banned_user.cfg | awk \'{print $3}\' | egrep "^\[U:[01]:[0-9]{3,12}\]$"');
	$aBanned = explode("\n", $ssh->get());

	// Содержимое banned_ip.cfg
	$ssh->set('cd '.$folder.' && cat banned_ip.cfg | awk \'{print $3}\' | egrep "(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}"');
	$aListip = explode("\n", $ssh->get());

	if(isset($aBanned[count($aBanned)-1]) AND $aBanned[count($aBanned)-1] == '')
		unset($aBanned[count($aBanned)-1]);

	if(isset($aListip[count($aListip)-1]) AND $aListip[count($aListip)-1] == '')
		unset($aListip[count($aListip)-1]);

	// Построение списка забаненых по steamid
	foreach($aBanned as $line => $steam)
	{
		$html->get('bans_list', 'sections/servers/games/settings');

			$html->set('value', trim($steam));

		$html->pack('banned');
	}

	// Построение списка забаненых по ip
	foreach($aListip as $line => $ip)
	{
		$html->get('bans_list', 'sections/servers/games/settings');

			$html->set('value', trim($ip));

		$html->pack('listip');
	}

	$html->get('bans', 'sections/servers/'.$server['game'].'/settings');

		$html->set('id', $id);
		$html->set('banned', isset($html->arr['banned']) ? $html->arr['banned'] : '');
		$html->set('listip', isset($html->arr['listip']) ? $html->arr['listip'] : '');

	$html->pack('main');
?>