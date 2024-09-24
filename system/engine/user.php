<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Подключение раздела
if (!in_array($section, ['auth', 'recovery', 'replenish', 'signup', 'lk', 'quit'])) {
    include(ENG . '404.php');
}

$aTitle = [
    'auth' => 'Авторизация',
    'recovery' => 'Восстановление',
    'replenish' => 'Пополнение баланса',
    'signup' => 'Регистрация',
    'lk' => 'Личный кабинет',
    'quit' => 'Выход',
];

$title = $aTitle[$section];

if ($section == 'lk') {
    $html->nav($title, $cfg['http'] . 'user/section/lk');
} else {
    $html->nav($title);
}

include(SEC . 'user/' . $section . '.php');
