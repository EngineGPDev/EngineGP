<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class users
	{
		public static function ava($user)
		{
			global $cfg;

			$file = 'upload/avatars/'.$user.'.';
			$link = $cfg['http'].'upload/avatars/'.$user.'.';

			if(file_exists(ROOT.$file.'jpg'))
				return $link.'jpg';

			if(file_exists(ROOT.$file.'png'))
				return $link.'png';

			if(file_exists(ROOT.$file.'gif'))
				return $link.'gif';

			return $cfg['http'].'template/images/avatar.png';
		}

		public static function nav($active)
		{
			global $cfg, $html;

			$aUnit = array('index', 'settings', 'auth', 'logs', 'security');

			$html->get('gmenu', 'sections/user');

				$html->set('home', $cfg['http']);

				foreach($aUnit as $unit)
					if($unit == $active) $html->unit($unit, 1); else $html->unit($unit);

			$html->pack('main');

			$html->get('vmenu', 'sections/user');

				$html->set('home', $cfg['http']);

				foreach($aUnit as $unit)
					if($unit == $active) $html->unit($unit, 1); else $html->unit($unit);

			$html->pack('vmenu');

			return NULL;
		}
	}
?>