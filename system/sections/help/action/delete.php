<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($user['group'] != 'admin')
		sys::outjs(array('e' => 'У вас нет доступа к данному действию.'));

	if($id)
	{
		$sql->query('DELETE FROM `help` WHERE `id`="'.$id.'" LIMIT 1');
		
		$dialogs = $sql->query('SELECT `id`, `img` FROM `help_dialogs` WHERE `help`="'.$id.'"');
		while($dialog = $sql->get($dialogs))
		{
			$aImg = sys::b64djs($dialog['img']);

			foreach($aImg as $img)
			{
				$sql->query('DELETE FROM `help_upload` WHERE `name`="'.$img.'" LIMIT 1');

				unlink(ROOT.'upload/'.$img);
			}

			$sql->query('DELETE FROM `help_dialogs` WHERE `id`="'.$dialog['id'].'" LIMIT 1');
		}

		sys::outjs(array('s' => 'ok'));
	}

	sys::outjs(array('e' => 'Вопрос не найден в базе.'));
?>