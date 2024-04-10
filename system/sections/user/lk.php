<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Проверка на авторизацию
sys::noauth();

// Генерация пароля
if (isset($url['passwd']))
    sys::out(sys::passwd(10));

$aTitle = array(
    'index' => 'Профиль',
    'settings' => 'Настройки',
    'auth' => 'Логи авторизаций',
    'logs' => 'История операций',
    'security' => 'Безопасность'
);

$url['subsection'] = isset($url['subsection']) ? $url['subsection'] : 'index';

// Подключение раздела
if (in_array($url['subsection'], array('index', 'settings', 'auth', 'logs', 'security', 'action', 'cashback'))) {
    $title = isset($aTitle[$url['subsection']]) ? $aTitle[$url['subsection']] : '';
    $html->nav($title);

    include(LIB . 'users.php');

    users::nav($url['subsection']);

    include(SEC . 'user/lk/' . $url['subsection'] . '.php');
} else
    include(ENG . '404.php');
