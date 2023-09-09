<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Проверка на авторизацию
sys::noauth();

$title = 'Онлайн чат';

require(SEC . 'chat/chats.php');
