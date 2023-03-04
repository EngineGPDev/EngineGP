<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aAction = array('script', 'sqlpasswd', 'proftpd', 'proftpd_modules', 'proftpd_sql', 'proftpd_passwd', 'proftpd_sqldump', 'rclocal', 'nginx', 'mysqlaptconfig', 'endinstall');

	if(!isset($url['action']) || !in_array($url['action'], $aAction))
		include(ENG.'404.php');

	$del = true;

	switch($url['action'])
	{
		case 'script':
			$sql->query('SELECT `id` FROM `control` WHERE `address`="'.$uip.'" LIMIT 1');
			if(!$sql->num())
				sys::out($uip);

			$del = false;
			$tmp = DATA.'control/egpautounit.sh';

			break;

		case 'sqlpasswd':
			$sql->query('SELECT `id`, `sql_passwd` FROM `control` WHERE `address`="'.$uip.'" LIMIT 1');
			if(!$sql->num())
				include(ENG.'404.php');

			$unit = $sql->get();

			if($unit['sql_passwd'])
				$tmp = sys::temp($unit['sql_passwd']);
			else{
				$passwd = sys::passwd();
				$tmp = sys::temp($passwd);
				$sql->query('UPDATE `control` set `sql_passwd`="'.$passwd.'" WHERE `id`="'.$unit['id'].'" LIMIT 1');
			}

			break;

		case 'proftpd_sql':
			$sql->query('SELECT `id`, `sql_passwd` FROM `control` WHERE `address`="'.$uip.'" LIMIT 1');
			if(!$sql->num())
				include(ENG.'404.php');

			$unit = $sql->get();

			$data = file_get_contents(DATA.'control/proftpd_sql.txt');
			$tmp = sys::temp(str_replace('[passwd]', $unit['sql_passwd'], $data));

			break;

		case 'proftpd_passwd':
			$sql->query('SELECT `id`, `sql_passwd` FROM `control` WHERE `address`="'.$uip.'" LIMIT 1');
			if(!$sql->num())
				include(ENG.'404.php');

			$unit = $sql->get();

			$data = file_get_contents(DATA.'control/proftpd_passwd.txt');
			$tmp = sys::temp(str_replace(array('[passwd]', '[passwd_ftp]'), array($unit['sql_passwd'], sys::passwd()), $data));

			break;

		case 'endinstall':
			$sql->query('UPDATE `control` set `status`="reboot" WHERE `address`="'.$uip.'" LIMIT 1');

			sys::out('ok:'.$uip);

		default:
			sys::outfile(DATA.'control/'.$url['action'].'.txt', $url['action']);
	}

	sys::outfile($tmp, $url['action'], $del);
?>