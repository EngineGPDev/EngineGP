<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Проверка на авторизацию
sys::noauth();

// Генерация пароля
if (isset($url['passwd']))
    sys::out(sys::passwd(10));

$aTitle = ['index' => 'Профиль', 'settings' => 'Настройки', 'auth' => 'Логи авторизаций', 'logs' => 'История операций', 'security' => 'Безопасность'];

$url['subsection'] ??= 'index';

// Подключение раздела
if (in_array($url['subsection'], ['index', 'settings', 'auth', 'logs', 'security', 'action', 'cashback'])) {
    $title = $aTitle[$url['subsection']] ?? '';
    $html->nav($title);

    require(LIB . 'users.php');

    users::nav($url['subsection']);

    require(SEC . 'user/lk/' . $url['subsection'] . '.php');
} else
    require(ENG . '404.php');
