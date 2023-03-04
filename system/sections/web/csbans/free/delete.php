<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	if(!$go)
		exit;

	if($user['group'] != 'admin')
		sys::outjs(array('i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'), $nmch);

	include(LIB.'web/free.php');

	$aData = array(
		'type' => $url['subsection'],
		'server' => array_merge($server, array('id' => $id))
	);

	web::delete($aData, $nmch);
?>