<?php
    if(!DEFINED('EGP'))
        exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));
	
	// Проверка на авторизацию
	sys::noauth();
	
    $title = 'Онлайн чат';

    include(SEC.'chat/chats.php');
?>