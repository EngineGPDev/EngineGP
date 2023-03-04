<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$sql->query('SELECT * FROM `plugins_update` WHERE `id`="'.$id.'" LIMIT 1');
	$plugin = $sql->get();

	$aGames = array(
		'cs' => 'Counter-Strike: 1.6',
		'cssold' => 'Counter-Strike: Source v34',
		'css' => 'Counter-Strike: Source',
		'csgo' => 'Counter-Strike: Global Offensive',
		'samp' => 'San Andreas Multiplayer',
		'crmp' => 'GTA: Criminal Russia',
		'mta' => 'Multi Theft Auto',
		'mc' => 'Minecraft'
	);

	if($go)
	{
		$aData = array();

		$aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : $plugin['name'];
		$aData['cat'] = isset($_POST['category']) ? sys::int($_POST['category']) : $plugin['cat'];
		$aData['status'] = isset($_POST['status']) ? sys::int($_POST['status']) : $plugin['status'];
		$aData['packs'] = isset($_POST['packs']) ? trim($_POST['packs']) : $plugin['packs'];
		$aData['desc'] = isset($_POST['desc']) ? trim($_POST['desc']) : $plugin['desc'];
		$aData['info'] = isset($_POST['info']) ? trim($_POST['info']) : $plugin['info'];
		$aData['images'] = isset($_POST['images']) ? trim($_POST['images']) : $plugin['images'];
		$aData['incompatible'] = isset($_POST['incompatible']) ? trim($_POST['incompatible']) : $plugin['incompatible'];
		$aData['choice'] = isset($_POST['choice']) ? trim($_POST['choice']) : $plugin['choice'];
		$aData['required'] = isset($_POST['required']) ? trim($_POST['required']) : $plugin['required'];
		$aData['update'] = isset($_POST['update']) ? sys::int($_POST['update']) : $plugin['update'];
		$aData['delete'] = isset($_POST['delete']) ? sys::int($_POST['delete']) : $plugin['delete'];
		$aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : $plugin['sort'];
		$aData['price'] = isset($_POST['price']) ? ceil($_POST['price']) : $plugin['price'];

		$aData['config_files_file'] = isset($_POST['config_files_file']) ? $_POST['config_files_file'] : array();
		$aData['config_files_sort'] = isset($_POST['config_files_sort']) ? $_POST['config_files_sort'] : array();
		$aData['config_clear_file'] = isset($_POST['config_clear_file']) ? $_POST['config_clear_file'] : array();
		$aData['config_clear_text'] = isset($_POST['config_clear_text']) ? $_POST['config_clear_text'] : array();
		$aData['config_write_file'] = isset($_POST['config_write_file']) ? $_POST['config_write_file'] : array();
		$aData['config_write_text'] = isset($_POST['config_write_text']) ? $_POST['config_write_text'] : array();
		$aData['config_write_top'] = isset($_POST['config_write_top']) ? $_POST['config_write_top'] : array();
		$aData['config_write_del_file'] = isset($_POST['config_write_del_file']) ? $_POST['config_write_del_file'] : array();
		$aData['config_write_del_text'] = isset($_POST['config_write_del_text']) ? $_POST['config_write_del_text'] : array();
		$aData['config_write_del_top'] = isset($_POST['config_write_del_top']) ? $_POST['config_write_del_top'] : array();
		$aData['files_delete_file'] = isset($_POST['files_delete_file']) ? $_POST['files_delete_file'] : array();

		$aData['cfg'] = 0;

		if($aData['name'] == '')
			sys::outjs(array('e' => 'Необходимо указать название'));

		$aPacks = explode(':', $aData['packs']);

		$spacks = '';

		foreach($aPacks as $packs)
		{
			$packs = trim($packs);

			$spacks .= intval($packs).':';
		}

		$spacks = isset($spacks{0}) ? substr($spacks, 0, -1) : '';

		$aData['packs'] = $spacks == '' ? $spacks : 'all';

		$aIncom = explode(':', $aData['incompatible']);

		$incoms = '';

		foreach($aIncom as $incom)
		{
			$incom = trim($incom);

			if(!is_numeric($incom))
				continue;

			$incoms .= intval($incom).':';
		}

		$incoms = isset($incoms{0}) ? substr($incoms, 0, -1) : '';

		$aData['incompatible'] = $incoms;

				$aChoice = explode(' ', $aData['choice']);

		$choice = '';

		foreach($aChoice as $chpl)
		{
			$aChpl = explode(':', $chpl);

			foreach($aChpl as $idchpl)
			{
				$idchpl = trim($idchpl);

				if(!is_numeric($idchpl))
					continue;

				$choice .= intval($idchpl).':';
			}

			$choice .= ' ';
		}

		$choice = isset($choice{0}) ? substr(trim($choice), 0, -1) : '';

		$aData['choice'] = $choice;

		$aRequi = explode(':', $aData['required']);

		$requis = '';

		foreach($aRequi as $requi)
		{
			$requi = trim($requi);

			if(!is_numeric($requi))
				continue;

			$requis .= intval($requi).':';
		}

		$requis = isset($requis{0}) ? substr($requis, 0, -1) : '';

		$aData['required'] = $requis;

		$n = 0;

		$sql->query('DELETE FROM `plugins_config` WHERE `update`="'.$id.'"');

		foreach($aData['config_files_file'] as $i => $file)
		{
			if($file == '')
				continue;

			$n+=1;

			$aData['config_files_sort'][$i] = $aData['config_files_sort'][$i] ? intval($aData['config_files_sort'][$i]) : $n;

			$sql->query('INSERT INTO `plugins_config` set `plugin`="'.$plugin['plugin'].'", `update`="'.$id.'", `file`="'.$file.'", `sort`="'.$n.'"');
		}

		if($n)
			$aData['cfg'] = 1;

		$sql->query('DELETE FROM `plugins_clear` WHERE `update`="'.$id.'"');

		foreach($aData['config_clear_file'] as $i => $file)
		{
			if($aData['config_clear_text'][$i] == '' || $file == '')
				continue;

			$regex = (string) $aData['config_clear_regex'] == 'on' ? 1 : 0;

			$text = htmlspecialchars(trim($aData['config_clear_text'][$i]));

			$sql->query('INSERT INTO `plugins_clear` set `plugin`="'.$plugin['plugin'].'", `update`="'.$id.'", `text`="'.$text.'", `file`="'.$file.'", `regex`="'.$regex.'"');
		}

		$sql->query('DELETE FROM `plugins_write` WHERE `update`="'.$id.'"');

		foreach($aData['config_write_file'] as $i => $file)
		{
			if($aData['config_write_text'][$i] == '' || $file == '')
				continue;

			$top = (string) $aData['config_write_top'][$i] == 'on' ? 1 : 0;

			$text = htmlspecialchars(trim($aData['config_write_text'][$i]));

			$sql->query('INSERT INTO `plugins_write` set `plugin`="'.$plugin['plugin'].'", `update`="'.$id.'", `text`="'.$text.'", `file`="'.$file.'", `top`="'.$top.'"');
		}

		$sql->query('DELETE FROM `plugins_write_del` WHERE `update`="'.$id.'"');

		foreach($aData['config_write_del_file'] as $i => $file)
		{
			if($aData['config_write_del_text'][$i] == '' || $file == '')
				continue;

			$top = (string) $aData['config_write_del_top'][$i] == 'on' ? 1 : 0;

			$text = htmlspecialchars(trim($aData['config_write_del_text'][$i]));

			$sql->query('INSERT INTO `plugins_write_del` set `plugin`="'.$plugin['plugin'].'", `update`="'.$id.'", `text`="'.$text.'", `file`="'.$file.'", `top`="'.$top.'"');
		}

		$sql->query('DELETE FROM `plugins_delete` WHERE `update`="'.$id.'"');

		foreach($aData['files_delete_file'] as $file)
		{
			if($file == '')
				continue;

			$sql->query('INSERT INTO `plugins_delete` set `plugin`="'.$plugin['plugin'].'", `update`="'.$id.'", `file`="'.$file.'"');
		}

		if($aData['delete'])
		{
			$sql->query('DELETE FROM `plugins_delete_ins` WHERE `update`="'.$id.'" LIMIT 1');

			$sql->query('INSERT INTO `plugins_delete_ins` set `plugin`="'.$plugin['plugin'].'", `update`="'.$id.'", `file`="'.$aData['delete'].'"');
		}

		$sql->query('UPDATE `plugins_update` set '
			.'`name`="'.htmlspecialchars($aData['name']).'",'
			.'`desc`="'.htmlspecialchars($aData['desc']).'",'
			.'`info`="'.htmlspecialchars($aData['info']).'",'
			.'`images`="'.htmlspecialchars($aData['images']).'",'
			.'`incompatible`="'.$aData['incompatible'].'",'
			.'`choice`="'.$aData['choice'].'",'
			.'`required`="'.$aData['required'].'",'
			.'`status`="'.$aData['status'].'",'
			.'`cfg`="'.$aData['cfg'].'",'
			.'`price`="'.$aData['price'].'",'
			.'`packs`="'.$aData['packs'].'" WHERE `id`="'.$id.'"');

		sys::outjs(array('s' => 'ok'));
	}

	$html->get('update', 'sections/addons');

		$html->set('id', $plugin['id']);
		$html->set('plugin', $plugin['plugin']);
		$html->set('name', $plugin['name']);
		$html->set('game', $aGames[$plugin['game']]);
		$html->set('desc', htmlspecialchars_decode($plugin['desc']));
		$html->set('info', htmlspecialchars_decode($plugin['info']));
		$html->set('images', htmlspecialchars_decode($plugin['images']));

		$html->set('incompatible', $plugin['incompatible']);
		$html->set('choice', $plugin['choice']);
		$html->set('required', $plugin['required']);
		$html->set('packs', $plugin['packs']);
		$html->set('sort', $plugin['sort']);
		$html->set('price', $plugin['price']);

		$status = '<option value="0">Стабильный</option><option value="1">Нестабильный</option><option value="2">Тестируемый</option>';

		$html->set('status', str_replace('"'.$plugin['status'].'">', '"'.$plugin['status'].'" selected>', $status));

		$sql->query('SELECT `name` FROM `plugins_category` WHERE `id`="'.$plugin['cat'].'" LIMIT 1');
		$cat = $sql->get();

		$html->set('category', $cat['name']);

		$config_files_all = '';
		$config_clear_all = '';
		$config_write_all = '';
		$config_write_del_all = '';
		$files_delete_all = '';

		$sql->query('SELECT `id`, `file`, `sort` FROM `plugins_config` WHERE `update`="'.$id.'" ORDER BY `sort` ASC');
		while($data = $sql->get())
		{
			$config_files_all .= '<tr id="cf_'.$data['id'].'">';
				$config_files_all .= '<td><input name="config_files_file['.$data['id'].']" value="'.$data['file'].'" type="text"></td>';
				$config_files_all .= '<td><input name="config_files_sort['.$data['id'].']" value="'.$data['sort'].'" type="text"></td>';
				$config_files_all .= '<td class="text-center"><a href="#" onclick="return config_files_del(\''.$data['id'].'\')" class="text-red">Удалить</a></td>';
			$config_files_all .= '</tr>';
		}

		$sql->query('SELECT `id`, `text`, `file`, `regex` FROM `plugins_clear` WHERE `update`="'.$id.'" ORDER BY `id` ASC');
		while($data = $sql->get())
		{
			$regex = $data['regex'] ? 'checked' : '';

			$config_clear_all .= '<tr id="cc_'.$data['id'].'">';
				$config_clear_all .= '<td><input name="config_clear_file['.$data['id'].']" value="'.$data['file'].'" type="text"></td>';
				$config_clear_all .= '<td><input name="config_clear_text['.$data['id'].']" value="'.$data['text'].'" type="text"></td>';
				$config_clear_all .= '<td class="text-center"><input name="config_clear_regex['.$data['id'].']" type="checkbox" '.$regex.'></td>';
				$config_clear_all .= '<td class="text-center"><a href="#" onclick="return config_clear_del(\''.$data['id'].'\')" class="text-red">Удалить</a></td>';
			$config_clear_all .= '</tr>';
		}

		$sql->query('SELECT `id`, `text`, `file`, `top` FROM `plugins_write` WHERE `update`="'.$id.'" ORDER BY `id` ASC');
		while($data = $sql->get())
		{
			$top = $data['top'] ? 'checked' : '';

			$config_write_all .= '<tr id="cw_'.$data['id'].'">';
				$config_write_all .= '<td><input name="config_write_file['.$data['id'].']" value="'.$data['file'].'" type="text"></td>';
				$config_write_all .= '<td><input name="config_write_text['.$data['id'].']" value="'.$data['text'].'" type="text"></td>';
				$config_write_all .= '<td class="text-center"><input name="config_write_top['.$data['id'].']" type="checkbox" '.$top.'></td>';
				$config_write_all .= '<td class="text-center"><a href="#" onclick="return config_write_del(\''.$data['id'].'\')" class="text-red">Удалить</a></td>';
			$config_write_all .= '</tr>';
		}

		$sql->query('SELECT `id`, `text`, `file`, `top` FROM `plugins_write_del` WHERE `update`="'.$id.'" ORDER BY `id` ASC');
		while($data = $sql->get())
		{
			$top = $data['top'] ? 'checked' : '';

			$config_write_del_all .= '<tr id="cwe_'.$data['id'].'">';
				$config_write_del_all .= '<td><input name="config_write_del_file['.$data['id'].']" value="'.$data['file'].'" type="text"></td>';
				$config_write_del_all .= '<td><input name="config_write_del_text['.$data['id'].']" value="'.$data['text'].'" type="text"></td>';
				$config_write_del_all .= '<td class="text-center"><input name="config_write_del_top['.$data['id'].']" type="checkbox" '.$top.'></td>';
				$config_write_del_all .= '<td class="text-center"><a href="#" onclick="return config_write_del_del(\''.$data['id'].'\')" class="text-red">Удалить</a></td>';
			$config_write_del_all .= '</tr>';
		}

		$sql->query('SELECT `id`, `file` FROM `plugins_delete` WHERE `update`="'.$id.'" ORDER BY `id` ASC');
		while($data = $sql->get())
		{
			$files_delete_all .= '<tr id="fd_'.$data['id'].'">';
				$files_delete_all .= '<td><input name="files_delete_file['.$data['id'].']" value="'.$data['file'].'" type="text"></td>';
				$files_delete_all .= '<td class="text-center"><a href="#" onclick="return files_delete_del(\''.$data['id'].'\')" class="text-red">Удалить</a></td>';
			$files_delete_all .= '</tr>';
		}

		$html->set('config_files_all', $config_files_all);
		$html->set('config_clear_all', $config_clear_all);
		$html->set('config_write_all', $config_write_all);
		$html->set('config_write_del_all', $config_write_del_all);
		$html->set('files_delete_all', $files_delete_all);

	$html->pack('main');
?>