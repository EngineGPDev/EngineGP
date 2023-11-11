<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!isset($nmch))
		$nmch = false;

	$text = isset($_POST['text']) ? $_POST['text'] : (isset($url['tag']) ? urldecode($url['tag']) : sys::outjs(array('none' => '')));

	$mkey = md5($text.'wiki');

	if($mcache->get($mkey) != '' AND !isset($url['tag']))
		sys::outjs(array('s' => $mcache->get($mkey)));

	if(!isset($text{2}) AND !isset($url['tag']))
		sys::outjs(array('s' => 'Для выполнения поиска, необходимо больше данных', $nmch));

	$aWiki_q = array();
	$aNswer_q = array();

	// Поиск по вопросу
	$wiki_q = $sql->query('SELECT `id` FROM `wiki` WHERE `name` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\') OR `tags` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\') LIMIT 3');

	// Поиск по тексту (ответу)
	$answer_q = $sql->query('SELECT `wiki` FROM `wiki_answer` WHERE `text` LIKE FROM_BASE64(\''.base64_encode('%'.$text.'%').'\') LIMIT 5');

	// Если нет ниодного совпадения по вводимому тексту
	if(!$sql->num($wiki_q) AND !$sql->num($answer_q) AND !isset($url['tag']))
	{
		// Поиск по словам
		if(!strpos($text, ' '))
		{
			$mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

			sys::outjs(array('s' => 'По вашему запросу ничего не найдено'));
		}

		// Массив слов
		$aText = explode(' ', $text);

		// Метка, которая изменится в процессе, если будет найдено хоть одно совпадение
		$sWord = false;

		foreach($aText as $word)
		{
			if($word == '' || !isset($word{2}))
				continue;

			// Поиск по вопросу
			$wiki_q = $sql->query('SELECT `id` FROM `wiki` WHERE `name` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\') OR `tags` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\') LIMIT 3');

			// Поиск по тексту (ответу)
			$answer_q = $sql->query('SELECT `wiki` FROM `wiki_answer` WHERE `text` LIKE FROM_BASE64(\''.base64_encode('%'.$word.'%').'\') LIMIT 5');

			if($sql->num($wiki_q))
				$aWiki_q[] = $wiki_q;

			if($sql->num($answer_q))
				$aNswer_q[] = $answer_q;
		}

	}else{
		$aWiki_q[] = $wiki_q;
		$aNswer_q[] = $answer_q;
	}

	if(!count($aWiki_q) AND !count($aNswer_q) AND !isset($url['tag']))
	{
		$mcache->set($mkey, 'По вашему запросу ничего не найдено', false, 15);

		sys::outjs(array('s' => 'По вашему запросу ничего не найдено'));
	}

	// Защита от дублирования
	$aResult = array();

	foreach($aWiki_q as $index => $wiki_q)
	{
		// Генерация списка (совпадение по вопросу)
		while($wiki = $sql->get($wiki_q))
		{
			// Проверка дублирования
			if(in_array($wiki['id'], $aResult))
				continue;

			$sql->query('SELECT `id`, `name`, `tags`, `date` FROM `wiki` WHERE `id`="'.$wiki['id'].'" LIMIT 1');
			$quest = $sql->get();

			$aTags = explode(',', $quest['tags']);

			$tags = '';

			foreach($aTags as $tag)
			{
				$tag = trim($tag);

				$tags .= '<a href="'.$cfg['http'].'wiki/section/search/tag/'.$tag.'">'.$tag.'</a>';
			}

			$html->get('list', 'sections/wiki/question');

				$html->set('id', $quest['id']);
				$html->set('name', sys::find($quest['name'], $text));
				$html->set('tags', $tags != '' ? $tags : 'Теги отсутствуют');
				$html->set('date', sys::today($quest['date']));

				$html->set('home', $cfg['http']);

			$html->pack('question');

			array_push($aResult, $wiki['id']);
		}
	}

	foreach($aNswer_q as $index => $answer_q)
	{
		// Генерация списка (совпадение по ответу)
		while($answer = $sql->get($answer_q))
		{
			// Проверка дублирования
			if(in_array($answer['wiki'], $aResult))
				continue;

			$sql->query('SELECT `id`, `name`, `tags`, `date` FROM `wiki` WHERE `id`="'.$answer['wiki'].'" LIMIT 1');
			$quest = $sql->get();

			$aTags = explode(',', $quest['tags']);

			$tags = '';

			foreach($aTags as $tag)
			{
				$tag = trim($tag);

				$tags .= '<a href="'.$cfg['http'].'wiki/section/search/tag/'.$tag.'">'.$tag.'</a>';
			}

			$html->get('list', 'sections/wiki/question');

				$html->set('id', $quest['id']);
				$html->set('name', sys::find($quest['name'], $text));
				$html->set('tags', $tags != '' ? $tags : 'Теги отсутствуют');
				$html->set('date', sys::today($quest['date']));

				$html->set('home', $cfg['http']);

			$html->pack('question');
		}
	}

	$html->arr['question'] = isset($html->arr['question']) ? $html->arr['question'] : 'По вашему запросу ничего не найдено';

	$mcache->set($mkey, $html->arr['question'], false, 15);

	sys::outjs(array('s' => $html->arr['question']), $nmch);
?>