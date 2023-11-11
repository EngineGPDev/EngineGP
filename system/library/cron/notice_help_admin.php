<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class notice_help_admin extends cron
	{
		function __construct()
		{
			global $cfg, $sql;

			$sql->query('SELECT `id`, `time`, `notice_admin` FROM `help` WHERE (`notice_admin`="0" OR `notice_admin`="2") AND `close`="0" LIMIT 1');
			if(!$sql->num())
				return NULL;

			$help = $sql->get();

			foreach($cfg['notice_admin'] as $id)
			{
				$sql->query('SELECT `mail` FROM `users` WHERE `id`="'.$id.'" LIMIT 1');
				$admin = $sql->get();

				if($help['notice_admin'] != 2)
				{
					if(!sys::mail('Техническая поддержка', sys::updtext(sys::text('mail', 'notice_help_admin_new'), array('url' => $cfg['http'].'help/section/dialog/id/'.$help['id'])), $admin['mail']))
						continue;
				}else{
					if(!sys::mail('Техническая поддержка', sys::updtext(sys::text('mail', 'notice_help_admin'), array('url' => $cfg['http'].'help/section/dialog/id/'.$help['id'])), $admin['mail']))
						continue;
				}
			}

			$sql->query('UPDATE `help` set `notice_admin`="1" WHERE `id`="'.$help['id'].'" LIMIT 1');

			return NULL;
		}
	}
?>