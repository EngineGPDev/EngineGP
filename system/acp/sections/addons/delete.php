<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($url['type']))
		exit;

	if($url['type'] == 'plugin')
	{
		$sql->query('DELETE FROM `plugins_config` WHERE `plugin`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_clear` WHERE `plugin`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_write` WHERE `plugin`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_write_del` WHERE `plugin`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_delete` WHERE `plugin`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_delete_ins` WHERE `plugin`="'.$id.'" LIMIT 1');
		$sql->query('DELETE FROM `plugins` WHERE `id`="'.$id.'" LIMIT 1');

		$sql->query('SELECT `id` FROM `plugins_update` WHERE `plugin`="'.$plugin['plugin'].'"');
		while($update = $sql->get())
		{
			unlink(FILES.'plugins/delete/u'.$update['id'].'.rm');
			unlink(FILES.'plugins/delete/'.$update['id'].'.rm');
			unlink(FILES.'plugins/install/u'.$update['id'].'.zip');
			unlink(FILES.'plugins/update/'.$update['id'].'.zip');
		}

		unlink(FILES.'plugins/delete/'.$id.'.rm');
		unlink(FILES.'plugins/install/'.$id.'.zip');

		$sql->query('DELETE FROM `plugins_update` WHERE `id`="'.$id.'"');
	}elseif($url['type'] == 'update'){
		$sql->query('DELETE FROM `plugins_config` WHERE `update`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_clear` WHERE `update`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_write` WHERE `update`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_write_del` WHERE `update`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_delete` WHERE `update`="'.$id.'"');
		$sql->query('DELETE FROM `plugins_delete_ins` WHERE `update`="'.$id.'" LIMIT 1');

		unlink(FILES.'plugins/delete/u'.$id.'.rm');
		unlink(FILES.'plugins/install/u'.$id.'.zip');
		unlink(FILES.'plugins/update/'.$id.'.zip');

		$sql->query('DELETE FROM `plugins_update` WHERE `id`="'.$id.'" LIMIT 1');

		$sql->query('SELECT `id` FROM `plugins_update` WHERE `plugin`="'.$plugin['plugin'].'" ORDER BY `id` DESC LIMIT 1');
		if($sql->num())
		{
			$update = $sql->get();

			$sql->query('UPDATE `plugins` set `upd`="'.$update['id'].'" WHERE `id`="'.$plugin['plugin'].'" LIMIT 1');
		}else
			$sql->query('UPDATE `plugins` set `upd`="0" WHERE `id`="'.$plugin['plugin'].'" LIMIT 1');
	}else{
		$sql->query('SELECT `id` FROM `plugins` WHERE `cat`="'.$id.'" LIMIT 1');
		if(!$sql->num())
			$sql->query('DELETE FROM `plugins_category` WHERE `id`="'.$id.'" LIMIT 1');
	}

	sys::outjs(array('s' => 'ok'));
?>