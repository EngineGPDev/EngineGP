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

// Подключение раздела
if (!in_array($section, array('auth', 'recovery', 'replenish', 'signup', 'lk', 'quit')))
    include(ENG . '404.php');

$aTitle = array(
    'auth' => 'Авторизация',
    'recovery' => 'Восстановление',
    'replenish' => 'Пополнение баланса',
    'signup' => 'Регистрация',
    'lk' => 'Личный кабинет',
    'quit' => 'Выход'
);

$title = $aTitle[$section];

if ($section == 'lk')
    $html->nav($title, $cfg['http'] . 'user/section/lk');
else
    $html->nav($title);

include(SEC . 'user/' . $section . '.php');
