<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class web_delete extends cron
	{
		function __construct()
		{
			global $argv, $sql;

			$sql->query('SELECT `id`, `login`, `type`, `server`, `unit` FROM `web` WHERE `id`="'.$argv[3].'" LIMIT 1');
			$web = $sql->get();

			if($web['type'] == 'hosting')
			{
				include(DATA.'web.php');

				$result = json_decode(file_get_contents(sys::updtext($aWebUnit['isp']['account']['delete'], array('login' => $web['login']))), true);

				if(!isset($result['result']) || strtolower($result['result']) != 'ok')
					continue;

				$sql->query('DELETE FROM `web` WHERE `id`="'.$web['id'].'" LIMIT 1');
			}

			include(LIB.'web/free.php');

			$aData = array(
				'type' => $web['type'],
				'server' => array('id' => $web['server'], 'unit' => $web['unit'], 'user' => 0, 'game' => 'system')
			);

			web::delete($aData, false);
		}
	}
?>