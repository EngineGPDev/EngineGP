<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	set_time_limit(1200);

	$aData = array();

	$aData['title'] = isset($_POST['title']) ? trim($_POST['title']) : sys::outjs(array('e' => 'Необходимо указать заголовок'));
	$aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : sys::outjs(array('e' => 'Необходимо указать сообщение'));

	$aData['users'] = isset($_POST['users']) ? $_POST['users'] : sys::outjs(array('e' => 'Необходимо указать получателей'));

	if($aData['title'] == '' || $aData['text'] == '')
		sys::outjs(array('e' => 'Необходимо заполнить все поля'));

	if(!is_array($aData['users']) || !count($aData['users']))
		sys::outjs(array('e' => 'Необходимо указать минимум одного получателя'));

	$noletter = '';

	include(LIB.'smtp.php');

	foreach($aData['users'] as $id => $cheked)
	{
		if($cheked != 'on')
			continue;

		$sql->query('SELECT `mail` FROM `users` WHERE `id`="'.sys::int($id).'" LIMIT 1');
		$us = $sql->get();

		$tpl = file_get_contents(DATA.'mail.ini', "r");

		$text = str_replace(
			array('[name]', '[text]', '[http]', '[img]', '[css]'),
			array($cfg['name'], $aData['text'], $cfg['http'], $cfg['http'].'template/images/', $cfg['http'].'template/css/'),
			$tpl
		);

		$smtp = new smtp($cfg['smtp_login'], $cfg['smtp_passwd'], $cfg['smtp_url'], $cfg['smtp_mail'], 465);

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=utf-8\r\n";
		$headers .= "From: ".$cfg['smtp_name']." <".$cfg['smtp_mail'].">\r\n";

		if(!$smtp->send($us['mail'], strip_tags($aData['title']), $text, $headers))
			$noletter .= '<p>'.$us['mail'].'</p>';
	}

	if($noletter == '')
		$noletter = 'отправлено всем.';

	sys::outjs(array('s' => $noletter));
?>