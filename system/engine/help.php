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

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Проверка на авторизацию
sys::noauth();

$sql->query('SELECT `id` FROM `users` WHERE `id`="' . $user['id'] . '" AND `help`="0" LIMIT 1');
if (!$sql->num()) {
    $html->nav('Техническая поддержка');

    $html->get('noaccess', 'sections/help');
    $html->pack('main');
} else {
    // Подключение раздела
    if (!in_array($section, array('create', 'dialog', 'open', 'close', 'notice', 'upload')))
        include(ENG . '404.php');

    $aNav = array(
        'help' => 'Техническая поддержка',
        'create' => 'Создание вопроса',
        'dialog' => 'Решение вопроса',
        'open' => 'Список открытых вопросов',
        'close' => 'Список закрытых вопросов'
    );

    $title = isset($aNav[$section]) ? $aNav[$section] : $section;
    $html->nav($aNav['help'], $cfg['http'] . 'help/section/open');
    $html->nav($title);

    include(SEC . 'help/' . $section . '.php');
}
