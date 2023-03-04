<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($nmch))
		$nmch = false;

	$text = isset($_POST['text']) ? $_POST['text'] : sys::outjs(array('none' => ''));

	$mkey = md5($sid.$text.$id);

	if($mcache->get($mkey) != '')
		sys::outjs(array('s' => $mcache->get($mkey)));

	if(!isset($text{2}))
		sys::outjs(array('s' => 'Для выполнения поиска, необходимо больше данных', $nmch));

	$sPlugins = array();
	$sUpdate = array();

	// Поиск по плагинам
	$plugins = $sql->query('SELECT `id`, `packs` FROM `plugins` WHERE `game`="'.$server['game'].'" AND `name` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\') OR `desc` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\') LIMIT 5');

	// Поиск по обновлениям
	$update = false;

	if(!$sql->num($plugins))
	{
		$plugins = $sql->query('SELECT `id`, `plugin`, `packs` FROM `plugins_update` WHERE `game`="'.$server['game'].'" AND (`name` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\') OR `desc` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\')) AND `upd`="0" LIMIT 5');
		$update = true;
	}

	// Если нет ниодного совпадения по вводимому тексту
	if(!$sql->num($plugins))
	{
		// Поиск по словам
		if(strpos($text, ' '))
		{
			// Массив слов
			$aText = explode(' ', $text);

			// Метка, которая изменится в процессе, если будет найдено хоть одно совпадение
			$sWord = false;

			foreach($aText as $word)
			{
				if($word == '' || !isset($word{2}))
					continue;

				// Поиск по плагинам
				$plugins = $sql->query('SELECT `id`, `packs` FROM `plugins` WHERE `name` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\') OR `desc` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\') LIMIT 5');

				// Поиск по обновлениям
				$update = false;

				if(!$sql->num($plugins))
				{
					$plugins = $sql->query('SELECT `id`, `plugin`, `packs` FROM `plugins_update` WHERE (`name` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\') OR `desc` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\')) AND `upd`="0" LIMIT 5');
					$update = true;
				}
				
				if($sql->num($plugins))
				{
					if(!$sWord) $sWord = true;

					$sPlugins[] = $plugins;
					$sUpdate[] = $update;
				}
			}

			// Если нет ниодного совпадения
			if(!$sWord)
			{
				$mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

				sys::outjs(array('s' => 'По вашему запросу ничего не найдено'));
			}
		}else{
			$mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

			sys::outjs(array('s' => 'По вашему запросу ничего не найдено'));
		}
	}else{
		$sPlugins[] = $plugins;
		$sUpdate[] = $update;
	}

	// Массив для исклуючения дублирования
	$aPlugins = array();
	
	foreach($sPlugins as $index => $plugins)
	{
		while($plugin = $sql->get($plugins))
		{
			// Проверка дублирования
			if(($sUpdate[$index] AND in_array($plugin['plugin'], $aPlugins)) || !$sUpdate[$index] AND in_array($plugin['id'], $aPlugins))
				continue;

			// Проверка на доступность плагина к установленной на сервере сборке
			$packs = strpos($plugin['packs'], ':') ? explode(':', $plugin['packs']) : array($plugin['packs']);
			if(!in_array($server['pack'], $packs) AND $plugin['packs'] != 'all')
				continue;

			$install = false; // не установлен плагин
			$upd = false; // не обновлен плагин

			if($sUpdate[$index])
			{
				$sql->query('SELECT `id`, `upd`, `time` FROM `control_plugins_install` WHERE `server`="'.$sid.'" AND `plugin`="'.$plugin['plugin'].'" LIMIT 1');

				$aPlugins[] = $plugin['plugin'];
			}else{
				$sql->query('SELECT `id`, `upd`, `time` FROM `control_plugins_install` WHERE `server`="'.$sid.'" AND `plugin`="'.$plugin['id'].'" LIMIT 1');

				$aPlugins[] = $plugin['id'];
			}

			// Проверка на установку
			if($sql->num())
			{
				$install = $sql->get();

				$upd = $install['upd'];
				$time = sys::today($install['time']);

				$install = true;
			}

			// Если установлен обновленный плагин
			if($upd)
				$sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins_update` WHERE `id`="'.$upd.'" LIMIT 1');
			else
				$sql->query('SELECT `name`, `desc`, `status`, `cfg`, `upd` FROM `plugins` WHERE `id`="'.$plugin['id'].'" LIMIT 1');

			$plugin = array_merge($plugin, $sql->get());

			$html->get('search', 'sections/control/servers/games/plugins');

				// Если установлен
				if($install)
				{
					// Если есть обновление
					if($plugin['upd'] > $upd) $html->unit('update', 1); else $html->unit('update');

					// Если есть редактируемые файлы
					if($plugin['cfg']) $html->unit('config', 1); else $html->unit('config');

					$html->unit('install', 1);
					$html->unit('!install');
				}else{
					// Обновление данных на более позднею версию плагина
					$sql->query('SELECT `name`, `desc`, `status`, `cfg` FROM `plugins_update` WHERE `plugin`="'.$plugin['id'].'" AND `upd`="0" LIMIT 1');
					if($sql->num())
					{
						$upd = $sql->get();

						$plugin['name'] = $upd['name'];
						$plugin['desc'] = $upd['desc'];
						$plugin['status'] = $upd['status'];
						$plugin['cfg'] = $upd['cfg'];
					}

					$html->unit('install');
					$html->unit('!install', 1);
				}

				if(!$plugin['status'])
				{
					$html->unit('unstable');
					$html->unit('stable', 1);
					$html->unit('testing');
				}elseif($plugin['status'] == 2){
					$html->unit('unstable');
					$html->unit('stable');
					$html->unit('testing', 1);
				}else{
					$html->unit('unstable', 1);
					$html->unit('stable');
					$html->unit('testing');
				}

				$html->set('id', $id);
				$html->set('server', $sid);
				$html->set('plugin', $plugin['id']);

				if($install)
					$html->set('time', $time);

				$html->set('name', sys::find(htmlspecialchars_decode($plugin['name']), $text));
				$html->set('desc', sys::find(htmlspecialchars_decode($plugin['desc']), $text));

			$html->pack('plugins');
		}
	}

	$html->arr['plugins'] = isset($html->arr['plugins']) ? $html->arr['plugins'] : '';

	$mcache->set($mkey, $html->arr['plugins'], false, 15);

	sys::outjs(array('s' => $html->arr['plugins']), $nmch);
?>