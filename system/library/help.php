<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	class help
	{
		public static function text($text)
		{
			$etext = '';

			$aStr = explode("\n", htmlspecialchars($text));

			foreach($aStr as $line => $str)
			{
				$check = str_replace(' ', '', $str);

				if(isset($aStr[$line+1]) AND ($check == '' AND str_replace(' ', '', $aStr[$line+1]) == ''))
					continue;
				else{
					$etext .= rtrim(str_replace("\t", '    ', $str))."\n";

					continue;
				}

				if($check != '')
					$etext .= rtrim(str_replace("\t", '    ', $str))."\n";
			}

			$str_search = array(
				"#\\\n#is",
				"#\[spoiler\](.+?)\[\/spoiler\]#is",
				"#\[sp\](.+?)\[\/sp\]#is",
				"#\[b\](.+?)\[\/b\]#is",
				"#\[u\](.+?)\[\/u\]#is",
				"#\[code\](.+?)\[\/code\]#is",
				"#\[quote\](.+?)\[\/quote\]#is",
				"#\[url=(.+?)\](.+?)\[\/url\]#is",
				"#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is"
			);

			$str_replace = array(
				"<br>",
				"<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'>\\1</div></div>",
				"<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'>\\1</div></div>",
				"<b>\\1</b>",
				"<u>\\1</u>",
				"<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'><pre><code>\\1</code></pre></div></div>",
				"<blockquote><p>\\1</p></blockquote>",
				"<a href='\\1' target='_blank'>\\2</a>",
				" <a href='\\2' target='_blank'>\\2</a>"
			);

			return preg_replace($str_search, $str_replace, $etext);
		}

		public static function ago($time, $brackets = false)
		{
			global $start_point;

			$diff = $start_point-$time;

			if($diff < 0)
				return '';

			if(!$diff)
				$diff = 1;

			$seconds = array('секунду', 'секунды', 'секунд');
			$minutes = array('минуту', 'минуты', 'минут');
			$hours = array('час', 'часа', 'часов');
			$days = array('день', 'дня', 'дней');
			$weeks = array('неделю', 'недели', 'недель');
			$months = array('месяц', 'месяца', 'месяцев');
			$years = array('год', 'года', 'лет');

			$phrase = array($seconds, $minutes, $hours, $days, $weeks, $months, $years);
			$length = array(1, 60, 3600, 86400, 604800, 2630880, 31570560);

			for($i = 6; ($i >= 0) AND (($no = $diff/$length[$i]) <= 1); $i-=1);

			if($i < 0)
				$i = 0;

			$_time = $start_point-($diff % $length[$i]);
			$no = ceil($no);

			if($brackets)
				return '('.$no.' '.help::parse_ago($no, $phrase[$i]).' назад)';

			return $no.' '.help::parse_ago($no, $phrase[$i]).' назад';
		}

		private static function parse_ago($number, $titles)
		{
			$cases = array(2, 0, 1, 1, 1, 2);

			return $titles[($number % 100 > 4 AND $number % 100 < 20 ) ? 2 : $cases[min($number % 10, 5)]];
		}
	}
?>