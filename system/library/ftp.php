<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class ftp
	{
		var $steck = false;

		var $dayru = array(
			'Mon' => 'Понедельник',
			'Tue' => 'Вторник',
			'Wed' => 'Среда',
			'Thu' => 'Четверг',
			'Fri' => 'Пятница',
			'Sat' => 'Суббота',
			'Sun' => 'Воскресенье'
		);

		var $mounthint = array(
			'Jan' => '01',
			'Feb' => '02',
			'Mar' => '03',
			'Apr' => '04',
			'May' => '05',
			'Jun' => '06',
			'Jul' => '07',
			'Aug' => '08',
			'Sep' => '09',
			'Oct' => '10',
			'Nov' => '11',
			'Dec' => '12'
		);

		var $mounthru = array(
			'Jan' => 'Янв',
			'Feb' => 'Фев',
			'Mar' => 'Мар',
			'Apr' => 'Апр',
			'May' => 'Май',
			'Jun' => 'Июн',
			'Jul' => 'Июл',
			'Aug' => 'Авг',
			'Sep' => 'Сен',
			'Oct' => 'Окт',
			'Nov' => 'Ноя',
			'Dec' => 'Дек'
		);

		var $aEdits = array(
			'txt',
			'cfg',
			'conf',
			'json',
			'xml',
			'ini',
			'gam',
			'php',
			'html',
			'inf',
			'js',
			'css',
			'sma',
			'log'
		);

		public function auth($host, $user, $password, $port = 21)
		{
			$ftp_connect = @ftp_connect($host, $port);

			if(!$ftp_connect)
				return false;

			if(!@ftp_login($ftp_connect, $user, $password))
				return false;

			@ftp_pasv($ftp_connect, true);

			$this->steck = $ftp_connect;

			return true;
		}

		public function read($path)
		{
			$path = ($path == '') ? '/' : $path;

			$path = str_replace('//', '/', $path);

			$aDir = array();
			$aFile = array();
			$aInfo = array();

			$rawlist = array();

			$data = ftp_rawlist($this->steck, $path);

			if(is_array($data))
				foreach($data as $index)
				{
					$vinfo = preg_split('/[\s]+/', $index, 9);

					if($vinfo[0] !== 'total')
					{
						$aInfo['chmod'] = $vinfo[0];
						$aInfo['num'] = $vinfo[1];
						$aInfo['owner'] = $vinfo[2];
						$aInfo['group'] = $vinfo[3];
						$aInfo['size'] = $vinfo[4];
						$aInfo['month'] = $vinfo[5];
						$aInfo['day'] = $vinfo[6];
						$aInfo['time'] = $vinfo[7];
						$aInfo['name'] = $vinfo[8];

						$rawlist[$aInfo['name']] = $aInfo;
					}
				}

			foreach($rawlist as $name => $data)
			{
				if($data['chmod']{0} == 'd')
					$aDir[$name] = $data;

				elseif($data['chmod']{0} == '-')
					$aFile[$name] = $data;
			}

			$aData = array(
				'folder' => $aDir,
				'file' => $aFile,
				'path' => $path
			);

			return $aData;
		}

		public function view($view, $server)
		{
			global $html;
			
			if($view['path'] != '/')
			{
				$html->get('filetp_back', 'sections/servers/games/filetp');

					$html->set('back', $this->path($view['path']));

				$html->pack('list');
			}

			foreach($view as $type => $aVal)
			{
				if(!is_array($aVal))
					continue;

				foreach($aVal as $name => $info)
				{
					$html->get('filetp_list', 'sections/servers/games/filetp');

						$html->set('id', $server);
						$html->set('name', $name);

						$path = $view['path'];

						if($path{0} != '/') $path = '/'.$path;

						if($path != '/') $path = $path.'/';

						$html->set('path', $path);
						$html->set('chmod', $this->cti($info['chmod']).' '.$info['chmod']);
						$html->set('owner', $info['owner']);
						$html->set('group', $info['group']);

						if($type == 'folder')
						{
							$html->unit('folder', 1);
							$html->unit('file');
							$html->set('size', '');
						}else{
							$type = explode('.', $name);

							if(in_array(end($type), $this->aEdits))
								$html->unit('edit', 1);
							else
								$html->unit('edit');

							$html->unit('file', 1);
							$html->unit('folder');
							$html->set('size', sys::size($info['size']));
						}

						$html->set('month', $this->mounthru[$info['month']]);
						$html->set('day', $info['day']);
						$html->set('time', $info['time']);

					$html->pack('list');
				}
			}

			return isset($html->arr['list']) ? $html->arr['list'] : '';
		}

		public function mkdir($path, $folders)
		{
			if(!@ftp_chdir($this->steck, $path))
				sys::outjs(array('e' => 'Ошибка: не удалось создать папку'));

			$aFolder = explode('/', $folders);

			foreach($aFolder as $folder)
			{
				if($folder == '')
					continue;

				if(!@ftp_chdir($this->steck, $folder))
				{
					if(!@ftp_mkdir($this->steck, $folder))
						sys::outjs(array('e' => 'Ошибка: не удалось создать папку '.$folder));

					@ftp_chdir($this->steck, $folder);
				}
			}

			sys::outjs(array('s' => 'ok'));
		}

		public function touch($path, $file, $text)
		{
			$aData = explode('/', $file);

			$path_file = '';

			if(count($aData))
			{
				$file = end($aData);

				unset($aData[count($aData)-1]);

				foreach($aData as $val)
					$path_file .= $val.'/';
			}

			$dir = str_replace('//', '', $path.'/'.$path_file);

			$dir = ($dir == '') ? '/' : $dir;

			if(!@ftp_chdir($this->steck, $dir))
				sys::outjs(array('e' => 'Ошибка: не удалось создать файл'));

			$temp = sys::temp($text);

			if(@ftp_put($this->steck, $file, $temp, FTP_BINARY))
			{
				unlink($temp);

				sys::outjs(array('s' => 'ok'));
			}

			unlink($temp);

			sys::outjs(array('e' => 'Ошибка: не удалось создать файл'));
		}

		public function edit_file($path, $file)
		{
			$name = md5(time().$file.'ftp');

			if(@ftp_get($this->steck, TEMP.$name, $path.'/'.$file, FTP_BINARY))
			{
				$data = file_get_contents(TEMP.$name);

				unlink(TEMP.$name);

				sys::outjs(array('s' => $data));
			}

			sys::outjs(array('e' => 'Не удалось открыть файл'));
		}

		public function rename($path, $oldname, $newname)
		{
			if(@ftp_rename($this->steck, $path.'/'.$oldname, $path.'/'.$newname))
				sys::outjs(array('s' => 'ok'));

			sys::outjs(array('e' => 'Не удалось сменить имя'));
		}

		public function rmdir($path, $folder)
		{
			if(@ftp_rmdir($this->steck, $path.'/'.$folder))
				sys::outjs(array('s' => 'ok'));

			sys::outjs(array('e' => 'Ошибка: не удалось удалить папку.'));
		}

		public function rmfile($file)
		{
			if(@ftp_delete($this->steck, $file))
				sys::outjs(array('s' => 'ok'));

			sys::outjs(array('e' => 'Ошибка: не удалось удалить файл'));
		}

		public function chmod($path, $name, $chmod)
		{
			if(ftp_site($this->steck, 'CHMOD 0'.$chmod.' '.$path.'/'.$name))
				sys::outjs(array('s' => 'ok'));

			sys::outjs(array('e' => 'Ошибка: не удалось изменить права.'));
		}

		public function search($str, $server)
		{
			global $html, $mcache;

			$nmch = md5($str.$server);

			$cache = $mcache->get($nmch);

			if(!is_array($cache))
			{
				$aData = ftp_rawlist($this->steck, '/', true);

				if(!is_array($aData))
					sys::out('Ничего не найдено');

				// Файлы
				$aFile = array();

				// Файлы в корне
				$end = array_search('', $aData);

				for($i = 0; $i < $end; $i+=1)
				{
					$aInfo = preg_split('/[\s]+/', $aData[$i], 9);

					$info = '';

					for($n = 0; $n < 8; $n+=1)
						$info .= $aInfo[$n].' ';

					$aFile['/'][] = array('info' => $info, 'name' => $aInfo[8]);
				}

				// Перебор директорий и файлов в них
				foreach($aData as $index)
				{
					$begin = array_search('', $aData);
					unset($aData[$begin]);

					$end = array_search('', $aData);

					if(!$begin)
						break;

					$dir = substr($aData[$begin+1], 0, -1);

					for($i = $begin+2; $i < $end; $i+=1)
					{
						$aInfo = preg_split('/[\s]+/', $aData[$i], 9);

						$info = '';

						for($n = 0; $n < 8; $n+=1)
							$info .= $aInfo[$n].' ';

						$aFile[$dir][] = array('info' => $info, 'name' => $aInfo[8]);
					}
				}

				$mcache->set($nmch, $aFile, false, 20);
			}else
				$aFile = $cache;

			$aFind = array();

			// Поиск
			foreach($aFile as $dir => $files)
			{
				foreach($files as $file)
				{
					$find = sys::first(explode('.', $file['name']));

					if(preg_match('/'.$str.'/i', $find))
						$aFind[] = array('dir' => $dir, 'info' => $file['info'], 'file' => $file['name'], 'find' => sys::find($file['name'], $str));
				}
			}

			unset($aFile);

			foreach($aFind as $data)
			{
				$info = preg_split('/[\s]+/', trim($data['info']), 8);

				$html->get('filetp_find', 'sections/servers/games/filetp');

					$html->set('id', $server);
					$html->set('find', $data['find']);
					$html->set('name', $data['file']);

					$path = $data['dir'];

					if($path{0} != '/') $path = '/'.$path;

					if($path != '/') $path = $path.'/';

					$html->set('path', $path);
					$html->set('chmod', $this->cti($info[0]).' '.$info[0]);
					$html->set('owner', $info[2]);
					$html->set('group', $info[3]);

					if($info[0]{0} == 'd')
					{
						$html->unit('folder', 1);
						$html->unit('file');
						$html->set('size', '');
					}else{
						$type = explode('.', $data['file']);

						if(in_array(end($type), $this->aEdits))
							$html->unit('edit', 1);
						else
							$html->unit('edit');

						$html->unit('file', 1);
						$html->unit('folder');
						$html->set('size', sys::size($info[4]));
					}

					$html->set('month', $this->mounthru[$info[5]]);
					$html->set('day', $info[6]);
					$html->set('time', $info[7]);

				$html->pack('list');
			}

			if(isset($html->arr['list']))
				sys::out($html->arr['list']);

			sys::out('Ничего не найдено');
		}

		public function logs($data, $uid)
		{
			global $html;

			$aLine = explode("\n", $data);

			$actions = array('i' => 'загрузка', 'o' => 'скачивание', 'd' => 'удаление');
			$acticon = array('i' => '<i class="fa fa-upload"></i>', 'o' => '<i class="fa fa-download"></i>', 'd' => '<i class="fa fa-times"></i>');

			unset($aLine[count($aLine)-1]);

			rsort($aLine);

			foreach($aLine as $line)
			{
				$aData = explode('\\', $line);

				$html->get('filetp_logs', 'sections/servers/games/filetp');

					$html->set('month', $this->mounthint[$aData[0]]);
					$html->set('day', $aData[1]);
					$html->set('time', $aData[2]);
					$html->set('year', $aData[3]);
					$html->set('who', $this->who($aData[4]));
					$html->set('size', sys::size($aData[5]));
					$html->set('file', str_replace('/servers/'.$uid.'/', '', $aData[6]));
					$html->set('action', $actions[$aData[7]]);
					$html->set('acticon', $acticon[$aData[7]]);

				$html->pack('logs');
			}

			return isset($html->arr['logs']) ? $html->arr['logs'] : 'Список логов отсутствует';
		}

		private function path($path)
		{
			$path = str_replace('//', '/', $path);

			$path = explode('/', $path);

			unset($path[count($path)-1]);

			$newpath = '/';

			foreach($path as $index => $val)
				if(count($path)-1 == $index) $newpath .= $val; else $newpath .= $val.'/';

			return str_replace('//', '/', $newpath);
		}

		private function cti($chmod)
		{
			$intchmod = array('-' => '0', 'r' => '4', 'w' => '2', 'x' => '1');

			$chmod = substr(strtr($chmod, $intchmod), 1);

			$split = str_split($chmod, 3);

			return array_sum(str_split($split[0])).array_sum(str_split($split[1])).array_sum(str_split($split[2]));
		}

		private function who($address)
		{
			global $cfg, $uip;

			if($address == $cfg['ip'])
				return 'панель управления';

			if($address == $uip)
				return 'вы';

			return $address;
		}
	}
?>