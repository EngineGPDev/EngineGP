<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$html->nav('Список подключенных серверов', $cfg['http'].'control');
	$html->nav('Список игровых серверов #'.$id, $cfg['http'].'control/id/'.$id);
	$html->nav($server['address'], $cfg['http'].'control/id/'.$id.'/server/'.$sid);

	// Подразделы
	$aSub = array('install', 'delete', 'update', 'plugin', 'config', 'search');

	// Если выбран подраздел
	if(isset($url['subsection']) AND in_array($url['subsection'], $aSub))
	{
		$html->nav('Плагины', $cfg['http'].'control/id/'.$id.'/server/'.$sid.'/section/plugins');

		$nmch = sys::rep_act('ctrl_server_plugins_go_'.$sid, 10);

		include(SEC.'control/servers/games/plugins/'.$url['subsection'].'.php');
	}else{
		$html->nav('Плагины');

		// Если есть кеш
		if($mcache->get('ctrl_server_plugins_'.$sid) != '')
			$html->arr['main'] = $mcache->get('ctrl_server_plugins_'.$sid);
		else{
			include(LIB.'games/plugins.php');

			// Категории
			$cats = $sql->query('SELECT `id`, `name` FROM `plugins_category` WHERE `game`="'.$server['game'].'" ORDER BY `sort` ASC');
			while($cat = $sql->get($cats))
			{
				// Плагины
				$plugins = $sql->query('SELECT `id`, `name`, `desc`, `images`, `status`, `upd`, `packs`, `price` FROM `plugins` WHERE `cat`="'.$cat['id'].'" ORDER BY `sort`, `id` ASC');
				while($plugin = $sql->get($plugins))
				{
					// Проверка, установлен ли плагин на сервер
					$sql->query('SELECT `id` FROM `control_plugins_install` WHERE `server`="'.$sid.'" AND `plugin`="'.$plugin['id'].'" LIMIT 1');
					if($sql->num())
						continue;

					// Проверка наличия обновленной версии плагина
					if($plugin['upd'])
					{
						$idp = $plugin['id'];

						$sql->query('SELECT `name`, `desc`, `images`, `status`, `packs`, `price` FROM `plugins_update` WHERE `plugin`="'.$plugin['id'].'" ORDER BY `id` DESC LIMIT 1');
						if($sql->num())
						{
							$plugin = $sql->get();

							$plugin['id'] = $idp;
						}else
							$plugin['upd'] = 0;
					}

					// Проверка на доступность плагина к установленной на сервере сборке
					$packs = strpos($plugin['packs'], ':') ? explode(':',$plugin['packs']) : array($plugin['packs']);
					if(!in_array($server['pack'], $packs) AND $plugin['packs'] != 'all')
						continue;

					$images = plugins::images($plugin['images'], $plugin['id']);

					if($plugin['price'])
					{
						$sql->query('SELECT `id` FROM `plugins_buy` WHERE `plugin`="'.$plugin['id'].'" AND `server`="'.$sid.'" LIMIT 1');
						$buy = $sql->num();
					}

					// Шаблон плагина
					$html->get('plugin', 'sections/control/servers/games/plugins');

						$html->set('id', $id);
						$html->set('server', $sid);
						$html->set('plugin', $plugin['id']);

						plugins::status($plugin['status']);

						$html->set('name', htmlspecialchars_decode($plugin['name']));
						$html->set('desc', htmlspecialchars_decode($plugin['desc']));

						if(!empty($images))
						{
							$html->unit('images', 1);
							$html->set('images', $images);
						}else
							$html->unit('images');

						if(!$buy AND $plugin['price'])
						{
							$html->unit('price', true, true);
							$html->set('price', $plugin['price']);
						}else
							$html->unit('price', false, true);

					$html->pack('plugins');
				}

				// Шаблон блока плагинов
				$html->get('category', 'sections/control/servers/games/plugins');

					$html->set('name', $cat['name']);
					$html->set('plugins', isset($html->arr['plugins']) ? $html->arr['plugins'] : 'Доступных для установки плагинов нет.', 1);

				$html->pack('addons');
			}

			unset($cats, $cat, $plugins, $plugin);

			// Список установленных плагинов на сервер (отдельный блок)
			$pl_ins = $sql->query('SELECT `plugin`, `upd`, `time` FROM `control_plugins_install` WHERE `server`="'.$sid.'" ORDER BY `plugin`');
			while($plugin = $sql->get($pl_ins))
			{
				$sql->query('SELECT `id` FROM `plugins` WHERE `id`="'.$plugin['plugin'].'" LIMIT 1');
				if(!$sql->num())
					continue;

				$isUpd = $plugin['upd'];

				// Если установлен обновленный плагин
				if($isUpd)
					$sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins_update` WHERE `id`="'.$isUpd.'" LIMIT 1');
				else
					$sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins` WHERE `id`="'.$plugin['plugin'].'" LIMIT 1');

				$plugin = array_merge($plugin, $sql->get());

				// Шаблон плагина
				$html->get('plugin_install', 'sections/control/servers/games/plugins');

					$html->set('id', $id);
					$html->set('server', $sid);
					$html->set('plugin', $plugin['plugin']);

					plugins::status($plugin['status']);

					if($plugin['cfg']) $html->unit('config', 1); else $html->unit('config');

					if($plugin['upd']) $html->unit('update', 1); else $html->unit('update');

					$html->set('name', htmlspecialchars_decode($plugin['name']));
					$html->set('time', sys::today($plugin['time']));
					$html->set('desc', htmlspecialchars_decode($plugin['desc']));

				$html->pack('install');
			}

			$html->get('plugins', 'sections/control/servers/games');

				$html->set('id', $id);
				$html->set('server', $sid);
				$html->set('addons', isset($html->arr['addons']) ? $html->arr['addons'] : '');
				$html->set('install', isset($html->arr['install']) ? $html->arr['install'] : 'Установленные плагины отсутствуют.');

			$html->pack('main');

			$mcache->set('ctrl_server_plugins_'.$sid, $html->arr['main'], false, 60);
		}
	}
?>