<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class sys
	{
		public static function url($all = true)
		{
			if($_SERVER['REQUEST_URI'] == '/')
				return $all ? NULL : 'index';

			$url = array();

			$string = str_replace('//', '/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
			$aUrl = explode('/', trim($string, ' /'));

			if(!$all)
				return $aUrl[0];

			unset($aUrl[0]);

			$i = 1;
			$m = count($aUrl)+1;

			for($i; $i < $m; $i+=1)
				$url[$aUrl[$i]] = isset($aUrl[++$i]) ? $aUrl[$i] : true;

			return $url;
		}

		public static function user($user)
		{
			global $sql, $start_point;

			if($user['time']+10 < $start_point)
				$sql->query('UPDATE `users` set `time`="'.$start_point.'" WHERE `id`="'.$user['id'].'" LIMIT 1');

			return NULL;
		}

		public static function users($users, $user, $authkey, $del = false)
		{
			global $mcache;

			if($del)
				unset($users[md5($user['login'].$user['authkey'].$user['passwd'])]);
			else
				$users[md5($user['login'].$user['authkey'].$user['passwd'])] = $user;

			$mcache->set('users_auth', $users, false, 1000);

			return NULL;
		}

		public static function nav($server, $sid, $active)
		{
			global $cfg, $html, $sql, $mcache, $start_point;

			$notice_sid = $mcache->get('notice_'.$sid);

			$notice = is_array($notice_sid) ? $notice_sid : $mcache->get('notice_'.$server['unit']);

			if(!is_array($notice))
			{
				$sql->query('SELECT `server`, `text`, `color` FROM `notice` WHERE `server`="'.$sid.'" AND `time`>"'.$start_point.'" ORDER BY `id` DESC LIMIT 1');

				if(!$sql->num())
					$sql->query('SELECT `unit`, `text`, `color` FROM `notice` WHERE `unit`="'.$server['unit'].'" AND `time`>"'.$start_point.'" ORDER BY `id` DESC LIMIT 1');

				if($sql->num())
				{
					$notice = $sql->get();

					$nmc = $notice['server'] ? 'notice_'.$sid : 'notice_'.$server['unit'];

					$mcache->set('notice_'.$nmc, $notice, false, 10);
				}else
					$mcache->set('notice_'.$server['unit'], NULL, false, 10);
			}

			$aUnit = array('index', 'console', 'settings', 'plugins', 'maps', 'owners', 'filetp', 'tarif', 'copy', 'graph', 'web', 'boost');

			$html->get('gmenu', 'sections/servers/'.$server['game']);

				$html->set('id', $sid);
				$html->set('home', $cfg['http']);

				if(is_array($notice))
				{
					global $device;

					if($device == '!mobile')
						$html->set('notice', '<div class="informer '.$notice['color'].' topifon">'.$notice['text'].'</div><div class="space"></div>');
					else
						$html->set('notice', '<div class="heading-style-1 container"><div class="smaller-text color-'.$notice['color'].'-light">'.$notice['text'].'</div><div class="heading-decoration bg-'.$notice['color'].'-light" style="margin-top: 0px"></div></div>');
				}else
					$html->set('notice', '');

				if($server['console_use']) $html->unit('console_use', 1); else $html->unit('console_use');
				if($server['plugins_use']) $html->unit('plugins_use', 1); else $html->unit('plugins_use');
				if($server['ftp_use']) $html->unit('ftp_use', 1); else $html->unit('ftp_use');
				if($server['stats_use']) $html->unit('graph_use', 1); else $html->unit('graph_use');
				if($server['web_use']) $html->unit('web_use', 1); else $html->unit('web_use');
				if($server['copy_use']) $html->unit('copy_use', 1); else $html->unit('copy_use');

				foreach($aUnit as $unit)
					if($unit == $active) $html->unit($unit, 1); else $html->unit($unit);

			$html->pack('main');

			$html->get('vmenu', 'sections/servers/'.$server['game']);

				$html->set('id', $sid);
				$html->set('home', $cfg['http']);

				if($server['console_use']) $html->unit('console_use', 1); else $html->unit('console_use');
				if($server['plugins_use']) $html->unit('plugins_use', 1); else $html->unit('plugins_use');
				if($server['ftp_use']) $html->unit('ftp_use', 1); else $html->unit('ftp_use');
				if($server['stats_use']) $html->unit('graph_use', 1); else $html->unit('graph_use');
				if($server['web_use']) $html->unit('web_use', 1); else $html->unit('web_use');
				if($server['copy_use']) $html->unit('copy_use', 1); else $html->unit('copy_use');

				foreach($aUnit as $unit)
					if($unit == $active) $html->unit($unit, 1); else $html->unit($unit);

			$html->pack('vmenu');

			return NULL;
		}

		public static function route($server, $inc, $go, $all = false)
		{
			global $device, $start_point;

			$dir = $device == '!mobile' ? '' : 'megp/';
			$use = true;

			if(in_array($inc, array('plugins', 'ftp', 'console', 'graph', 'copy', 'web')))
			{
				$server['graph_use'] = $server['stats_use'];

				if(!$server[$inc.'_use'])
					$use = false;
			}

			if(!$use || $server['time'] < $start_point || in_array($server['status'], array('install', 'reinstall', 'update', 'recovery', 'blocked')))
			{
				if($go)
					sys::out('Раздел недоступен');

				if(!$use)
					return SEC.$dir.'servers/'.$server['game'].'/index.php';

				return SEC.$dir.'servers/noaccess.php';
			}

			if($all)
				return SEC.'servers/games/'.$inc.'.php';

			if(!file_exists(SEC.$dir.'servers/'.$server['game'].'/'.$inc.'.php'))
				return SEC.$dir.'servers/'.$server['game'].'/index.php';

			return SEC.$dir.'servers/'.$server['game'].'/'.$inc.'.php';
		}

		public static function int($data, $width = false)
		{
			if($width)
				return preg_replace("([^0-9]{0, ".$width."})", '', $data);

			return preg_replace("([^0-9])", '', $data);
		}

		public static function b64js($data)
		{
			return base64_encode(json_encode($data));
		}

		public static function b64djs($data)
		{
			return json_decode(base64_decode($data), true);
		}

		public static function hb64($data)
		{
			return base64_encode(htmlspecialchars($data));
		}

		public static function hb64d($data)
		{
			return htmlspecialchars_decode(base64_decode($data));
		}

		public static function outjs($val, $cache = false)
		{
			global $mcache;

			if($cache)
				$mcache->delete($cache);

			die(json_encode($val));
		}

		public static function out($val = '', $cache = false)
		{
			global $mcache;

			if($cache)
				$mcache->delete($cache);

			die(''.$val.'');
		}

		public static function outhtml($text, $time = 3, $url = false, $cache = false)
		{
			global $device, $mcache, $html, $cfg;

			if($cache)
				$mcache->delete($cache);

			$tpl = $device == '!mobile' ? '' : '/megp';

			$html->get('out');

				$html->set('title', $cfg['name']);
				$html->set('home', $cfg['http']);
				$html->set('css', $cfg['http'].'template'.$tpl.'/css/');
				$html->set('js', $cfg['http'].'template'.$tpl.'/js/');
				$html->set('img', $cfg['http'].'template'.$tpl.'/images/');
				$html->set('text', $text);

			$html->pack('out');

			if(!$url)
				$url = $cfg['http'];

			header('Refresh: '.$time.'; URL='.$url);

			die($html->arr['out']);
		}

		public static function valid($val, $type, $preg = '')
		{
			switch($type)
			{
				case 'promo':
					if(!preg_match("/^[A-Za-z0-9]{2,20}$/", $val))
						return true;

					return false;

				case 'en':
					if(!preg_match("/^[A-Za-z0-9]$/", $val))
						return true;

					return false;

				case 'ru':
					if(!preg_match("/^[А-Яа-я]$/u", $val))
						return true;

					return false;

				case 'wm':
					if(!preg_match('/^R[0-9]{12,12}$|^Z[0-9]{12,12}$|^U[0-9]{12,12}$/m', $val))
						return true;

					return false;

				case 'ip':
					if(!preg_match("/^(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}$/", $val))
						return true;

					return false;

				case 'steamid':
					if(!preg_match("/^STEAM_[0-9]:[0-9]:[0-9]{6,12}$|^HLTV$|^STEAM_ID_LAN$|^STEAM_ID_PENDING$|^VALVE_ID_LAN$|^VALVE_ID_PENDING$|^STEAM_666:88:666$/", $val))
						return true;

					return false;

				case 'steamid3':
					if(!preg_match("/^\[U:[01]:[0-9]{3,12}\]$/i", $val))
						return true;

					return false;

				case 'num':
					if(!preg_match('/[^0-9]/', $val))
						return true;

					return false;

				case 'md5':
					if(!preg_match("/^[a-z0-9]{32,32}$/", $val))
						return true;

					return false;

				case 'other':
					if(!preg_match($preg, $val))
						return true;

					return false;
			}

			return true;
		}

		public static function mail($name, $text, $mail)
		{
			global $cfg;

			require_once(LIB.'smtp.php');

			$tpl = file_get_contents(DATA.'mail.ini', "r");

			$text = str_replace(
				array('[name]', '[text]', '[http]', '[img]', '[css]'),
				array($cfg['name'], $text, $cfg['http'], $cfg['http'].'template/images/', $cfg['http'].'template/css/'),
				$tpl
			);

			$smtp = new smtp($cfg['smtp_login'], $cfg['smtp_passwd'], $cfg['smtp_url'], $cfg['smtp_mail'], 465);

			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
			$headers .= "From: ".$cfg['smtp_name']." <".$cfg['smtp_mail'].">\r\n";

			if($smtp->send($mail, $name, $text, $headers))
				return true;

			return false;
		}

		public static function mail_domain($mail)
		{
			$domain = explode('@', $mail);

			$domain = end($domain);

			if(in_array($domain, array('list.ru', 'bk.ru', 'inbox.ru')))
				$domain = 'mail.ru';

			switch($domain)
			{
				case 'mail.ru':
					return $domain;

				case 'yandex.ru':
					return 'mail.yandex.ru';

				case 'google.com':
					return 'mail.google.com';

				default:
					return '';
			}
		}

		public static function domain($domain)
		{
			$domain = explode('.', $domain);

			unset($domain[0]);

			return implode('.', $domain);
		}

		public static function updtext($text, $data)
		{
			foreach($data as $name => $val)
				$text = str_replace('['.$name.']', $val, $text);

			return $text;
		}

		public static function login($mail, $lchar)
		{
			if(!$lchar)
				return str_replace(array('.', '_', '+', '-'), '', sys::first(explode('@', $mail)));

			$list = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuWwXxYyZz0123456789';
			$a = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuWwXxYyZz';
			$selections = strlen($list)-1;
			$start = strlen($a)-1;
			$b = rand(0, $start);
			$start = $a[$b];
			$login = array();

			$i = 0;

			for($i; $i <= 10; $i+=1)
			{
				$n = rand(0, $selections);
				$login[] = $list[$n];
			}

			return $start.implode('', $login);
		}

		public static function passwd($length = 8)
		{
			$list = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuWwXxYyZz0123456789';
			$a = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuWwXxYyZz';
			$selections = strlen($list)-1;
			$start = strlen($a)-1;
			$b = rand(0, $start);
			$start = $a[$b];
			$passwd = array();

			$i = 0;

			for($i; $i <= $length-2; $i+=1)
			{
				$n = rand(0, $selections);
				$passwd[] = $list[$n];
			}

			return $start.implode('', $passwd);
		}

		public static function passwdkey($passwd)
		{
			return md5($passwd);
		}

		public static function cookie($name, $value, $expires)
		{
			$expires = time() + ($expires * 86400);
			setcookie($name, $value, $expires, "/", $_SERVER['HTTP_HOST'], null, true);
		}

		public static function auth()
		{
			global $auth, $go, $text, $cfg;

			if($auth)
			{
				if($go)
					sys::outjs(array('e' => sys::text('output', 'auth')));

				global $device;

				$link = $device == '!mobile' ? 'user/section/lk' : '';

				exit(header('Refresh: 0; URL='.$cfg['http'].$link));
			}

			return NULL;
		}

		public static function noauth()
		{
		   global $auth, $go, $text, $cfg;

			if(!$auth)
			{
				if($go)
					sys::outjs(array('e' => sys::text('output', 'noauth')));

				global $device;

				$link = $device == '!mobile' ? 'user/section/auth' : 'auth';

				exit(header('Refresh: 0; URL='.$cfg['http'].$link));
			}

			return NULL;
		}

		public static function browser($agent)
		{
			if(strpos($agent, 'Firefox') !== false)
				return 'Mozilla Firefox';

			if(strpos($agent, 'Opera') !== false)
				return 'Opera';

			if(strpos($agent, 'Chrome') !== false)
				return 'Google Chrome';

			if(strpos($agent, 'MSIE') !== false)
				return 'Internet Explorer';

			if(strpos($agent, 'Safari') !== false)
				return 'Safari';

			return 'Неизвестный';
		}

		public static function date($lenght, $date)
		{
			global $start_point;

			$check_time = $date-$start_point;

			if($check_time < 1)
				return 'время истекло.';

			$days = floor($check_time/86400);
			$hours = floor(($check_time%86400)/3600);
			$minutes = floor(($check_time%3600)/60);
			$seconds = $check_time%60; 

			$adata = array(
				'min' => array(
					'days' => array('день', 'дня', 'дней'),
					'hours' => array('ч.', 'ч.', 'ч.'),
					'minutes' => array('мин.', 'мин.', 'мин.'),
					'seconds' => array('сек.', 'сек.', 'сек.')
				),
				'max' => array(
					'days' => array('день', 'дня', 'дней'),
					'hours' => array('час', 'часа', 'часов'),
					'minutes' => array('минуту','минуты','минут'),
					'seconds' => array('секунду','секунды','секунд')
				)
			);

			$text = '';

			if($days > 0)
				$text .= sys::date_decl($days, $adata[$lenght]['days']);

			if($days < 1 AND $hours > 0)
				$text .= ' '.sys::date_decl($hours, $adata[$lenght]['hours']);

			if($days < 1 AND $minutes > 0)
				$text .= ' '.sys::date_decl($minutes, $adata[$lenght]['minutes']);

			if($days < 1 AND $seconds > 0)
				$text .= ' '.sys::date_decl($seconds, $adata[$lenght]['seconds']);

			return $text;
		}

		public static function date_decl($digit, $expr, $onlyword = false)
		{
			if(!is_array($expr))
				$expr = array_filter(explode(' ', $expr));

			if(empty($expr[2]))
				$expr[2] = $expr[1];

			$i = sys::int($digit)%100;

			if($onlyword)
				$digit = '';

			if($i > 4 AND $i < 21)
				$res = $digit.' '.$expr[2];
			else
				$i%=10;

			if($i == 1)
				$res = $digit.' '.$expr[0];
			elseif($i > 1 AND $i < 5)
				$res = $digit.' '.$expr[1];
			else
				$res = $digit.' '.$expr[2];

			return trim($res);
		}

		public static function today($time, $cp = false)
		{
			global $start_point;

			$today = date('d.m.Y', $start_point);
			$day = date('d.m.Y', $time);

			if($day == $today)
			{
				if($cp)
					return 'Сегодня '.date('H:i', $time);

				return 'Сегодня '.date('- H:i', $time);
			}

			$yesterday_first = sys::int(sys::first(explode('.', $today)))-1;
			$yesterday_full = date('m.Y', $time);

			if($day == $yesterday_first.'.'.$yesterday_full AND !$yesterday_first)
			{
				if($cp)
					return 'Вчера '.date('H:i', $time);

				return 'Вчера '.date('- H:i', $time);
			}

			if($cp)
				return date('d.m.Y H:i', $time);

			return date('d.m.Y - H:i', $time);
		}

		public static function day($time)
		{
			$days = array('день', 'дня', 'дней');

			$time = $time % 100; 

			if($n > 10 AND $n < 20) 
				return $days[2];

			$time = $time % 10;

			if($time > 1 AND $time < 5)
				return $days[1];

			if($time == 1) 
				return $days[0];

			return $days[2];
		}

		public static function bbc($text)
		{
			global $cfg;

			$lines = explode("\n", $text);

			$str_search = array(
			  "#\\\n#is",
			  "#\[spoiler\](.+?)\[\/spoiler\]#is",
			  "#\[sp\](.+?)\[\/sp\]#is",
			  "#\[b\](.+?)\[\/b\]#is",
			  "#\[u\](.+?)\[\/u\]#is",
			  "#\[code\](.+?)\[\/code\]#is",
			  "#\[quote\](.+?)\[\/quote\]#is",
			  "#\[url=(.+?)\](.+?)\[\/url\]#is",
			  "#\[img=(.+?)\] \[\/img\]#is",
			  "#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is"
			);

			$str_replace = array(
			  "<br />",
			  "<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'>\\1</div></div>",
			  "<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'>\\1</div></div>",
			  "<b>\\1</b>",
			  "<u>\\1</u>",
			  "<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'><pre><code>\\1</code></pre></div></div>",
			  "<blockquote><p>\\1</p></blockquote>",
			  "<a href='\\1' target='_blank'>\\2</a>",
			  "<a href='\\1' target='_blank' style='display: block;'><img src='".$cfg['url']."template/images/help_screenshot.png' alt='Изображение'></a>",
			  "<a href='\\2' target='_blank'> \\2</a>"
			);

			$uptext = '';

			foreach($lines as $line)
				$uptext .= preg_replace($str_search, $str_replace, $line)."<br>";

			return $uptext;
		}

		public static function first($array = array())
		{
			return $array[0];
		}

		public static function back($url)
		{
			exit(header('Refresh: 0; URL='.$url));
		}

		public static function strlen($str)
		{
			return iconv_strlen($str, 'UTF-8');
		}

		public static function text($section, $name)
		{
			global $cfg, $user;

			$group = isset($user['group']) ? $user['group'] : 'user';

			if($section != 'error' || !$cfg['text_group'])
				$group = 'all';

			include(DATA.'text/'.$section.'.php');

			return isset($text[$name][$group]) ? $text[$name][$group] : $text[$name];
		}

		public static function key($param = 'defegp')
		{
			return md5(sha1(rand(1, 15).$param.rand(16, 30).rand(200, 1000).rand(1, 100)));
		}

		public static function captcha($type, $ip)
		{
			global $mcache;

			$cod = '';
			$width = 100;
			$height = 45;
			$font_size = 16;
			$symbols = 3;
			$symbols_fon = 20;
			$font = LIB.'captcha/text.ttf';

			$chars = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z','2','3','4','5','6','7','9');
			$colors = array('20','50','80','100');

			$src = imagecreatetruecolor($width, $height);
			$fon = imagecolorallocate($src, 255, 255, 255);

			imagefill($src, 0, 0, $fon);

			$i = 0;
			for($i; $i < $symbols_fon; $i+=1)
			{
			   $color = imagecolorallocatealpha($src, rand(0,255), rand(0,255), rand(0,255), 100); 
			   $char = $chars[rand(0, sizeof($chars)-1)];
			   $size = rand($font_size-2, $font_size+2);

			   imagettftext($src, $size, rand(0,45), rand($width*0.1,$width-$width*0.1), rand($height*0.2,$height), $color, $font, $char);
			}

			$i = 0;
			for($i; $i < $symbols; $i+=1)
			{
			   $color = imagecolorallocatealpha($src, $colors[rand(0,sizeof($colors)-1)], $colors[rand(0,sizeof($colors)-1)], $colors[rand(0,sizeof($colors)-1)], rand(20,40)); 
			   $char = $chars[rand(0, sizeof($chars)-1)];
			   $size = rand($font_size*2.1-2, $font_size*2.1+2);

			   $x = ($i+1)*$font_size + rand(6,8);
			   $y = (($height*2)/3) + rand(3,7);

			   $cod .= $char;

			   imagettftext($src, $size, rand(0,15), $x, $y, $color, $font, $char);
			}

			$mcache->set($type.'_captcha_'.$ip, $cod, false, 120);

			header("Content-type: image/gif"); 
			imagegif($src);
			imagedestroy($src);
			exit;
		}

		public static function captcha_check($type, $ip, $cod = '')
		{
			global $cfg, $mcache;

			// Если повтор ввода капчи выключен и в кеше есть подтвержденный сеанс
			if(!$cfg['recaptcha'] AND $mcache->get($type.'_captcha_valid_'.$ip))
				return false;

			if($mcache->get($type.'_captcha_'.$ip) != strtolower($cod))
			{
				$mcache->set($type.'_captcha_valid_'.$ip, true, false, 60);

				return true;
			}

			return false;
		}

		public static function ismail($data)
		{
			$aData = explode('@', $data);

			if(count($aData) > 1)
				return true;

			return false;
		}

		public static function smscode()
		{
			return rand(1,9).rand(100,500).rand(10,99);
		}

		public static function code($length = 8)
		{
			$list = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuWwXxYyZz0123456789';
			$a = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuWwXxYyZz';
			$selections = strlen($list)-1;
			$start = strlen($a)-1;
			$b = rand(0, $start);
			$start = $a[$b];
			$code = array();

			$i = 0;

			for($i; $i <= $length-2; $i+=1)
			{
				$n = rand(0, $selections);
				$code[] = $list[$n];
			}

			return $start.implode('', $code);
		}

		public static function sms($text, $phone)
		{
			global $cfg;

			$out = file_get_contents($cfg['sms_gateway'].'&'.$cfg['sms_to'].'='.$phone.'&'.$cfg['sms_text'].'='.urlencode($text));

			$aOut = explode("\n", $out);

			if(trim($aOut[0]) == $cfg['sms_ok'])
				return true;

			return false;
		}

		public static function find($text, $find)
		{
			$words = explode(' ', $find);

			foreach($words as $word)
				if(strlen($word) >= 2)
					$text = preg_replace('#'.quotemeta($word).'#iu', '<span style="color: #F66A6A;">$0</span>', $text);

			return $text;
		}

		public static function str_first_replace($search, $replace, $text)
		{
		   $pos = strpos($text, $search);
		   
		   return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text; 
		}

		public static function cmd($command)
		{
			$text = preg_replace('/\\$/', '$ы', trim($command));

			mb_internal_encoding('UTF-8');

			if(mb_substr($text, -1) == 'ы')
				$text = quotemeta(substr($text, 0, -2));

			return $text;
		}

		public static function map($map)
		{
			$name = quotemeta(trim($map));

			if(substr($name, -1) == '$')
				$name = substr($name, 0, -2).'$';

			return str_replace(array('\.', '\*'), array('.', '*'), $name);
		}

		public static function temp($text)
		{
			$temp = TEMP.md5(time().rand(5, 100).rand(10, 20).rand(1, 20).rand(40, 80));

			$file = fopen($temp, "w");

			fputs($file, $text);

			fclose($file);

			return $temp;
		}

		public static function size($val)
		{
			$aSize = array(' Байт', ' Кб', ' Мб', ' Гб', ' Тб', ' Пб');

			return $val ? round($val/pow(1024, ($i = floor(log($val, 1024)))), 2) . $aSize[$i] : '0 Байт';
		}

		public static function unidate($date)
		{
			$aDate = explode('-', $date);

			$aFirst = explode(' ', $aDate[2]);

			return $aFirst[1].' - '.$aFirst[0].'.'.$aDate[1].'.'.$aDate[0];
		}

		public static function page($page, $nums, $num)
		{
			$ceil = ceil($nums/$num);

			if($page > $ceil)
				$page = $ceil;

			$next = $page*$num;

			if($next <= $nums)
				$next = $next-$num;

			if($next > $nums)
				$next = $next-$num;

			if($next < 1)
				$next = 0;

			$num_go = $next;
			if($page == '')
				$page = 1;

			$aPage = array(
				'page' => $page,
				'num' => $num_go,
				'ceil' => $ceil
			);

			return $aPage;
		}

		public static function page_list($countnum, $actnum)
		{
			if($countnum == 0 || $countnum == 1)
				return array();

			if($countnum > 10)
			{
				if($actnum <= 4 || $actnum + 3 >= $countnum)
				{
					for($i = 0; $i <= 4; $i++)
						$numlist[$i] = $i + 1;

					$numlist[5] = '...';
					for($j = 6, $k = 4; $j <= 10; $j+=1, $k-=1)
						$numlist[$j] = $countnum - $k;
				}else{
					$numlist[0] = 1;
					$numlist[1] = 2;
					$numlist[2] = '...';
					$numlist[3] = $actnum - 2;
					$numlist[4] = $actnum - 1;
					$numlist[5] = $actnum;
					$numlist[6] = $actnum + 1;
					$numlist[7] = $actnum + 2;
					$numlist[8] = '...';
					$numlist[9] = $countnum - 1;
					$numlist[10] = $countnum;
				}
			}else
				for($n = 0; $n < $countnum; $n+=1)
					$numlist[$n] = $n + 1;

			return $numlist;
		}

		public static function page_gen($ceil, $page, $actnum, $section)
		{
			global $cfg, $html;

			$aNum = sys::page_list($ceil, $actnum);

			$pages = '';

			$html->get('pages');

				if($ceil)
				{
					if($page != 1)
					{
						$next = $page-1;
						$pages .= '<a href="'.$cfg['http'].$section.'/page/'.$next.'"><span>Предыдущая</span></a>';
					}

					foreach($aNum as $v)
					{
						if($v != $page && $v != '...')
							$pages .= '<a href="'.$cfg['http'].$section.'/page/'.$v.'">'.$v.'</a>';
						
						if($v == $page)
							$pages .= '<a href="#" onclick="return false" class="active">'.$v.'</a>';
						
						if($v == '...')
							$pages .= '<a href="#" onclick="return false">...</a>';
					}

					if($ceil > $page)
					{
						if($page < $ceil)
						{
							$next = $page+1;
							$pages .= '<a href="'.$cfg['http'].$section.'/page/'.$next.'"><span class="num_right">Следующая</span></a>';
						}else
							$pages .= '<a href="#" onclick="return false;"><span class="num_right">Следующая</span></a>';
					}
				}

				$html->set('pages', $pages);

			$html->pack('pages');

			return NULL;
		}

		public static function country($name)
		{
			global $cfg;

			$fileimg = file_exists(TPL.'/images/country/'.$name.'.png');

			if($fileimg)
				return $cfg['http'].'template/images/country/'.$name.'.png';

			return $cfg['http'].'template/images/country/none.png';
		}

		public static function ipproxy()
		{
			global $_SERVER;

			if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP']))
				return $_SERVER['HTTP_CF_CONNECTING_IP'];

			return NULL;
		}

		public static function ip()
		{
			$ip = sys::ipproxy();

			if(sys::valid($ip, 'ip'))
				return $_SERVER['REMOTE_ADDR'];

			return $ip;
		}

		public static function whois($ip)
		{
			$stack = fsockopen('whois.ripe.net', 43, $errno, $errstr);

			if(!$stack)
				return 'не определена';

			fputs($stack, $ip."\r\n");

			$subnetwork = '';

			while(!feof($stack))
			{
				$str = fgets($stack, 128);

				if(strpos($str, 'route:') !== FALSE)
				{
					$subnetwork = trim(str_replace('route:', '', $str));

					break;
				}
			}

			fclose($stack);

			return isset($subnetwork{0}) ? $subnetwork : 'не определена';
		}

		public static function rep_act($name, $time = 20)
		{
			global $go, $mcache;

			if(!$go)
				return NULL;

			if($mcache->get($name))
				sys::outjs(array('e' => sys::text('other', 'mcache')));

			$mcache->set($name, true, false, $time);

			return $name;
		}

		public static function check_php_config($file, &$error)
		{
			exec('php -l '.$file, $error, $code);

			if(!$code)
				return true;

			return false;
		}

		public static function cpu_idle($pros_stat = array(), $unit, $fcpu = false, $ctrl = false)
		{
			return sys::cpu_get_idle(sys::parse_cpu($pros_stat[0]), sys::parse_cpu($pros_stat[1]), $unit, $fcpu, $ctrl);
		}

		public static function cpu_get_idle($first, $second, $unit, $fcpu, $ctrl)
		{
			global $sql;

			if(count($first) !== count($second))
				return;

			$cpus = array();

			for($i = 0, $l = count($first); $i < $l; $i+=1)
			{
				$dif = array();
				$dif['use'] = $second[$i]['use']-$first[$i]['use'];
				$dif['nice'] = $second[$i]['nice']-$first[$i]['nice'];
				$dif['sys'] = $second[$i]['sys']-$first[$i]['sys'];
				$dif['idle'] = $second[$i]['idle']-$first[$i]['idle'];
				$total = array_sum($dif);
				$cpu = array();

				foreach($dif as $x => $y)
					$cpu[$x] = $y ? round($y/$total*100, 1) : 0;

				$cpus['cpu'.$i] = $cpu;
			}

			if($fcpu)
				return $cpus;

			$threads = array();

			$l = count($first);

			for($i = 0; $i < $l; $i+=1)
				$threads[$i] = $cpus['cpu'.$i]['idle'];

			if(count($first) > 1)
				unset($threads[0]);

			$max = max($threads);

			foreach($threads as $idle)
			{
				$core = array_search($max, $threads);

				if($ctrl)
					$sql->query('SELECT `id` FROM `control_servers` WHERE `unit`="'.$unit.'" AND `core_fix`="'.($core+1).'" LIMIT 1');
				else
					$sql->query('SELECT `id` FROM `servers` WHERE `unit`="'.$unit.'" AND `core_fix`="'.($core+1).'" AND `core_fix_one`="1" LIMIT 1');
				if($sql->num())
				{
					unset($threads[$core]);

					if(!count($threads))
						return NULL;

					$max = max($threads);
				}
			}

			return array_search($max, $threads);
		}

		public static function parse_cpu($data)
		{
			$data = explode("\n", $data);

			$cpu = array();

			foreach($data as $line)
			{
				if(preg_match('/^cpu[0-9]/', $line))
				{
					$info = explode(' ', $line);

					$cpu[] = array(
						'use' => $info[1],
						'nice' => $info[2],
						'sys' => $info[3],
						'idle' => $info[4]
					);
				}
			}

			return $cpu;
		}

		public static function reset_mcache($nmch, $id, $data = array(), $ctrl = false)
		{
			global $mcache;

			$cache = array(
				'name' => $data['name'],
				'status' => sys::status($data['status'], $data['game']),
				'online' => $data['online'],
				'image' => '<img src="'.sys::status($data['status'], $data['game'], '', 'img').'">',
			);

			$cache = $ctrl ? sys::buttons($id, $data['status'], $data['game'], $ctrl) :  sys::buttons($id, $data['status'], $data['game']);

			if(isset($data['players']))
				$cache['players'] = $data['players'];

			$mcache->set($nmch, $cache, false, 5);

			return NULL;
		}

		public static function status($status, $game, $map = '', $get = 'text')
		{
			global $cfg;

			switch($status)
			{
				case 'working':
					if($get == 'img')
					{
						if(in_array($game, array('samp', 'crmp', 'mta', 'mc')))
							$map = $game;

						return sys::img($map, $game);
					}

					return 'Карта: '.($map == '' ? '-' : $map);

				case 'off':
					if($get == 'img')
						return $cfg['http'].'template/images/status/off.jpg';

					return 'Статус: <span style="color: #C46666;">выключен</span>';

				case 'start':
					if($get == 'img')
						return $cfg['http'].'template/images/status/start.gif';

					return 'Статус: <span style="color: #22B93C;">запускается</span>';

				case 'restart':
					if($get == 'img')
						return $cfg['http'].'template/images/status/restart.gif';

					return 'Статус: <span style="color: #22B93C;">перезапускается</span>';

				case 'change':
					if($get == 'img')
						return $cfg['http'].'template/images/status/change.gif';

					return 'Статус: <span style="color: #52BEFC;">меняется карта</span>';

				case 'install':
					if($get == 'img')
						return $cfg['http'].'template/images/status/install.gif';

					return 'Статус: <span style="color: #22B93C;">устанавливается</span>';

				case 'reinstall':
					if($get == 'img')
						return $cfg['http'].'template/images/status/reinstall.gif';

					return 'Статус: <span style="color: #22B93C;">переустанавливается</span>';

				case 'update':
					if($get == 'img')
						return $cfg['http'].'template/images/status/update.gif';

					return 'Статус: <span style="color: #F2CF41;">обновляется</span>';

				case 'recovery':
					if($get == 'img')
						return $cfg['http'].'template/images/status/recovery.gif';

					return 'Статус: <span style="color: #22B93C;">восстанавливается</span>';

				case 'overdue':
					if($get == 'img')
						return $cfg['http'].'template/images/status/overdue.jpg';

					return 'Статус: просрочен';

				case 'blocked':
					if($get == 'img')
						return $cfg['http'].'template/images/status/blocked.jpg';

					return 'Статус: заблокирован';
			}
		}

		public static function img($name, $game)
			{
			global $cfg;
			
				$filename = 'http://cdn.enginegp.ru/maps/'.$game.'/'.$name.'.jpg';
				$file_headers = @get_headers($filename) ;
				$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://";
				if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' ||trim($file_headers[0]) == 'HTTP/1.1 403 Forbidden') {
				        return $cfg['http'].'template/images/status/none.jpg';
				}
				else {
					return '' . $protocol .'cdn.enginegp.ru/maps/'.$game.'/'.$name.'.jpg';
				}
		}

		public static function buttons($id, $status, $game = false, $ctrl = false)
		{
			global $html;

			if(isset($html->arr['buttons']))
				unset($html->arr['buttons']);

			$other = in_array($game, array('samp', 'crmp', 'mta', 'mc'));

			$dir = $ctrl ? 'control/servers' : 'servers';

			if(in_array($status, array('working', 'change', 'start', 'restart')))
			{
				$html->get('stop', 'sections/'.$dir.'/buttons');

					$html->set('id', $id);
					if($ctrl)
						$html->set('ctrl', $ctrl);

				$html->pack('buttons');

				$html->get('restart', 'sections/'.$dir.'/buttons');

					$html->set('id', $id);
					if($ctrl)
						$html->set('ctrl', $ctrl);

				$html->pack('buttons');

				if(!$other)
				{
					$html->get('change', 'sections/'.$dir.'/buttons');

						$html->set('id', $id);
						if($ctrl)
							$html->set('ctrl', $ctrl);

					$html->pack('buttons');
				}

				return $html->arr['buttons'];
			}

			if($status == 'off')
			{
				$html->get('start', 'sections/'.$dir.'/buttons');

					$html->set('id', $id);
					if($ctrl)
						$html->set('ctrl', $ctrl);

				$html->pack('buttons');

				$html->get('reinstall', 'sections/'.$dir.'/buttons');

					$html->set('id', $id);
					if($ctrl)
						$html->set('ctrl', $ctrl);

				$html->pack('buttons');

				if(!$other)
				{
					$html->get('update', 'sections/'.$dir.'/buttons');

						$html->set('id', $id);
						if($ctrl)
							$html->set('ctrl', $ctrl);

					$html->pack('buttons');
				}

				return $html->arr['buttons'];
			}

			$html->get('other', 'sections/'.$dir.'/buttons');
			$html->pack('buttons');

			return $html->arr['buttons'];
		}

		public static function entoru($month)
		{
			$ru = array(
				1 => 'Янв', 2 => 'Фев', 3 => 'Мар', 4 => 'Апр',
				5 => 'Май', 6 => 'Июн', 7 => 'Июл', 8 => 'Авг',
				9 => 'Сен', 10 => 'Окт', 11 => 'Ноя', 12 => 'Дек'
			);

			return $ru[$month];
		}

		public static function head($head)
		{
			global $route, $header;

			if($head == 'description')
			{
				global $description;

				if(isset($description))
				{
					$text = str_replace(array('"', '-'), array('', '—'), strip_tags($description));

					if(strlen($text) > 160)
					{
						mb_internal_encoding('UTF-8');

						$text = mb_substr($text, 0, 157).'...';
					}

					return $text;
				}
			}else{
				global $keywords;

				if(isset($keywords))
					return str_replace(array('"', '-'), array('', '—'), strip_tags($keywords));
			}

			return array_key_exists($route, $header) ? $header[$route][$head] : $header['index'][$head];
		}

		public static function tags($tags)
		{
			$aTags = explode(',', $tags);

			$text = '';

			foreach($aTags as $tag)
				$text .= '<strong>'.trim($tag).'</strong>, ';

			return isset($text{0}) ? substr($text, 0, -2) : 'отсутствуют';
		}

		public static function benefitblock($id, $nmch = false)
		{
			global $cfg, $sql, $start_point;

			if($cfg['benefitblock'])
			{
				$sql->query('SELECT `benefit` FROM `servers` WHERE `id`="'.$id.'" LIMIT 1');
				$info = $sql->get();

				if($info['benefit'] > $start_point)
					sys::outjs(array('e' => 'Операция недоступна до '.date('d.m.Y - H:i:s', $info['benefit'])), $nmch);
			}

			return NULL;
		}

		function outfile($file, $name, $del = false)
		{
			if(file_exists($file)) 
			{
				if(ob_get_level())
					ob_end_clean();

				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$name);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: '.filesize($file));

				readfile($file);

				if($del)
					unlink($file);

				exit;
			}
		}
	}
?>