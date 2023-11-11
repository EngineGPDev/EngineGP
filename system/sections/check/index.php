<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		$badCommands = [
			'exec',
			'zzz',
			'SVC_DIRECTOR',
			'SVC_STUFFTEXT',
			'gl_*',
			'r_*',
			'hud_*',
			'cl_*', 
			'm_*', 
			'_restart', 
			'_set_vid_level', 
			'_setaddons_folder', 
			'_setgamedir', 
			'_sethdmodels', 
			'_setrenderer', 
			'_setvideomode', 
			'rate', 
			'connect', 
			'cmd', 
			'retry', 
			'timerefresh', 
			'alias', 
			'bind', 
			'abcdefghijklmnopqrstu', 
			'unbind', 
			'unbindall', 
			'cd', 
			'vzlom', 
			'flag', 
			'exec', 
			'exit', 
			'kill', 
			'quit', 
			'say', 
			'setinfo', 
			'sensitivity', 
			'sys_ticrate', 
			'writecfg', 
			'removedemo', 
			'ex_interp', 
			'developer', 
			'fps_max', 
			'speak_enabled', 
			'voice_enable', 
			'volume', 
			'mp3volume', 
			'motd_write',
			'dem_save'
		];

		if(isset($_POST) && !empty($_POST)){
		
			if(!isset($_FILES['file'])){
				sys::outjs(array('e' => 'Необходимо указать проверяемый файл.'));
			}
			
			$file = $_FILES['file'];
			
			if(substr($file['name'], -4) != '.sma'){
				sys::outjs(array('e' => 'Только .sma разрешается загружать'));
			}
			
			$text = file_get_contents($file['tmp_name']);
			$textArray = explode("\n", $text);
			
			$errors = [];
			
			foreach($textArray as $key => $str){
				$strNum = $key + 1;
				
				foreach($badCommands as $cmd){
					if(strpos($str, $cmd) !== false){
						$errors[$strNum] = $cmd;
					}
				}
			}
			
			if(!empty($errors)){
				$outputErrors .= '<thead><tr><th>Номер строки</th><th>Вредоносный код</th></tr></thead><tbody>';
				foreach($errors as $key => $msg){
					$outputErrors .= '<tr><td style="text-align: center;">'.$key.'</td><td style="text-align: center;">'.$msg.'</td></tr>';
				}
				$outputErrors .= '</tbody>';
			}
			else {
				if(!isset($outputErrors)){
					sys::outjs(array('s' => 'Вредоносный код не найден.'));
				}
			}
			sys::outjs(array('sma' => $outputErrors));
		}
	}

	$html->nav('Проверка плагинов на наличие бэкдоров');
	$html->get('check', 'sections/check');
	
	$html->pack('main');
?>