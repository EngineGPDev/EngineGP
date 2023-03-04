<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$title = 'Вакансии';

	$sql->query('SELECT `name` FROM `jobs` WHERE `id`="'.$id.'" AND `status`!="0" LIMIT 1');
	$nav = $sql->get();

	$html->nav('Вакансии', $cfg['http'].'jobs');
	$html->nav($nav['name']);

	if($id)
	{
		$sql->query('SELECT * FROM `jobs` WHERE `id`="'.$id.'" AND `status`!="0" LIMIT 1');
		if(!$sql->num())
			sys::back($cfg['http'].'jobs');

		$jobs = $sql->get();

		if($go)
		{
			$sql->query('SELECT `id` FROM `jobs_app` WHERE `user`="'.$user['id'].'" AND `job`="'.$id.'" LIMIT 1');
			if($sql->num())
				sys::outjs(array('e' => 'Вы уже подали заявку, ожидайте, пожалуйста, ответа от Администрации.'));

			if($_POST['contact'] == '')
				sys::outjs(array('e' => 'Необходимо указать контакты для связи!'));

			sys::noauth();
			
			$sql->query('INSERT INTO `jobs_app` set'
				.'`user`="'.$user['id'].'",'
				.'`text`="",'
				.'`contact`="'.$_POST['contact'].'",'
				.'`job`="'.$id.'",'
				.'`date`="'.$start_point.'"');

			sys::outjs(array('s' => 'ok'));
		}

		$sql->query('SELECT `text` FROM `jobs_app` WHERE `user`="'.$user['id'].'" AND `job`="'.$jobs['id'].'" LIMIT 1');
		$text = $sql->get();

		$html->get('jobs', 'jobs');
			$html->set('id', $jobs['id']);
			$html->set('name', $jobs['name']);
			$html->set('job', $jobs['job']);
			$html->set('desc', $jobs['desc']);
			$html->set('date', sys::today($jobs['date']));
			if(sys::strlen($text['text']) > 0)
			{
				$html->unit('answer', 1, 1);
				$html->set('text', $text['text']);
			} else {
				$html->unit('answer', 0, 1);
				$html->set('text', '');
			}		
		$html->pack('main');
	} else {
		$sql->query('SELECT * FROM `jobs` WHERE `status`!="0" ORDER BY `id` ASC');
		while($jobs = $sql->get())
		{
			$html->get('list', 'jobs');
				$html->set('id', $jobs['id']);
				$html->set('name', $jobs['name']);
				$html->set('job', $jobs['job']);
				$html->set('desc', $jobs['desc']);
				$html->set('date', sys::today($jobs['date']));
				if($id) $html->unit('close', 1, 1); else $html->unit('close', 0, 1);
			$html->pack('jobs_list');
		}
	}

	$html->get('index', 'jobs');
		if(!$id)
			$html->set('jobs', isset($html->arr['jobs_list']) ? $html->arr['jobs_list'] : '<div class="informer red center">На данный момент у нас нет свободных вакансий.</div>');
		else
			$html->set('jobs', '');

	$html->pack('main');
?>