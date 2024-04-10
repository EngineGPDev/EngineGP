<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
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
