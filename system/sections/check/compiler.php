<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if($go)
	{
		function createPostString($aPostFields) 
		{
        	foreach($aPostFields as $key => $value) 
                $aPostFields[$key] = urlencode($key).'='. urlencode($value);
	
			return implode('&', $aPostFields);
		}

		$sql->query('SELECT `browser` FROM `users` WHERE `id`="'.$user['id'].'" LIMIT 1');
		$u_sql = $sql->get();

		$browser = base64_decode($u_sql['browser']);

		$file = $_FILES['file_code'];
		
		if(substr($file['name'], -4) != '.sma')
			sys::outjs(array('e' => 'Только .sma разрешается загружать'));

		$text = file_get_contents($file['tmp_name']);
		$textArray = explode("\n", $text);

		$postFields['fname'] = $file['name'];
		$postFields['scode'] = $textArray;
		$postFields['go'] = 'send';
	
		$ch = curl_init('http://amxmodx.org/webcompiler.cgi');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; pl; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3');
		curl_setopt($ch, CURLOPT_POSTFIELDS, createPostString($postFields));
		$tresc = curl_exec($ch);
		
		sys::out($tresc);
	
		curl_close($ch);
						
		if(strpos($tresc, 'Your plugin successfully compiled!'))
		{
	        $tresc = substr($tresc, strpos($tresc, "http://www.amxmodx.org/webcompiler.cgi?"));
	        $ile = strpos($tresc, "</a>");
	        $link = substr($tresc, 0, $ile);
	        $tresc = substr($tresc, strpos($tresc, "Welcome to the AMX Mod X"));
	        $ile = strpos($tresc, "</pre>");
	        $inf = substr($tresc, 0, $ile);
	        $inf = str_replace("\r\n","<br/ >", $inf);

		    $out = '
				<br><center>Ваш плагин скомпилирован <span class="compiller_good"><b>удачно</b></span><br><br>
		        	Чтобы скачать пройдите по <a href="'.$link.'"><b>ссылке</b></a><br />

				<a href="javascript:void(0)" onclick="ShowHideLog(\'block_id\')">Посмотреть лог компиляции</a><br/><br/>
						<div id="block_id" style="display: none;">
    							<pre>'.$inf.'</pre>
					</div>
		        	</center>';

			$good = "good.txt";

			if(!file_exists(FILES.$good))
			{
				$handle = fopen(FILES.$good, "w");
				$count_good = 0;
				fwrite($handle, $count_good);
				fclose($handle);
			}else{
				$file = file(FILES.$good);
				$count_good = $file[0];
			}

			$count_good++;
		
			$handle = fopen(FILES.$good, "w");
			fwrite($handle, $count_good);
			fclose($handle);
		}else{
	        $ktory = strpos($tresc, "Your plugin failed to compile");
	        $tresc = substr($tresc, $ktory + 63);
	        $ile = strpos($tresc, "</pre>");
	        $tresc = substr($tresc, 0, $ile);

		    $out = '
				<br><center>Ваш плагин скомпилирован <span class="compiller_failed"><b>неудачно</b></span><br><br>

				<a href="javascript:void(0)" onclick="ShowHideLog(\'block_id\')">Посмотреть лог ошибок</a><br/><br/>
						<div id="block_id" style="display: none;">
    							<pre>'.$tresc.'</pre>
					</div>
		        	</center>
			';

			$failed = "failed.txt";

			if(!file_exists(FILES.$failed))
			{
				$handle = fopen(FILES.$failed, "w");
				$count_failed = 0;
				fwrite($handle, $count_failed);
				fclose($handle);
			}else{
				$file = file(FILES.$failed);
				$count_failed = $file[0];
			}

			$count_failed++;
		
			$handle = fopen(FILES.$failed, "w");
			fwrite($handle, $count_failed);
			fclose($handle);
		}
	}else{
		$good = "good.txt";
		$failed = "failed.txt";
	
		if(!file_exists(FILES.$good))
		{
			$handle = fopen(FILES.$good, "w");
			$count_good = 0;
			fwrite($handle, $count_good);
			fclose($handle);
		}else{
			$file = file(FILES.$good);
			$count_good = $file[0];
		}
	
		if(!file_exists(FILES.$failed))
		{
			$handle = fopen(FILES.$failed, "w");
			$count_failed = 0;
			fwrite($handle, $count_failed);
			fclose($handle);
		}else{
			$file = file(FILES.$failed);
			$count_failed = $file[0];
		}
		
		$html->get('compiler', 'sections/check');
			$html->set('success', $count_good);
			$html->set('failed', $count_failed);
		$html->pack('compilers');

		include(SEC.'check/index.php');
	}
?>