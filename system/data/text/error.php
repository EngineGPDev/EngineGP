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

$text = array(
    'ssh' => array(
        // Если вывод ошибок по группе пользователя
        'admin' => 'Не удалось создать соединение с оборудованием.',

        'support' => 'Проблемы на линии связи между панелью и оборудованием.',

        'user' => 'Не удалось выполнить операцию, ошибка #1, обратитесь в тех.поддержку.',

        // Если вывод ошибок не учитывать группу пользователя
        'all' => 'Не удалось создать соединение с оборудованием.'
    ),

    'cpu' => array(
        // Если вывод ошибок по группе пользователя
        'admin' => 'Не удается запустить игровой сервер, нет свободного потока.',

        'support' => 'Не удается запустить игрвоой сервер, нет свободного потока.',

        'user' => 'Не удалось выполнить операцию, ошибка #с404, обратитесь в тех.поддержку.',

        // Если вывод ошибок не учитывать группу пользователя
        'all' => 'Не удается запустить игровой сервер, нет свободного потока.'
    ),

    'mail' => 'Не удалось отправить сообщение на почту.',

    'signup' => 'Подача регистрации не найдена.',

    'recovery' => 'Подача восстановления не найдена.',

    'ser_stop' => 'Выключение невозможно, сервер должен быть включен.',
    'ser_start' => 'Включение невозможно, сервер должен быть выключен.',
    'ser_restart' => 'Перезагрузка невозможна, сервер должен быть включен.',
    'ser_reinstall' => 'Переустановка невозможна, сервер должен быть выключен.',
    'ser_update' => 'Обновление невозможно, сервер должен быть выключен.',
    'ser_change' => 'Смена карты невозможна, сервер должен быть полностью запущен.',
    'ser_change_go' => 'Не удалось сменить карту.',
    'ser_owner' => 'У вас нет доступа к данной операции.',
    'ser_delete' => 'Удаление невозможно, сервер должен быть выключен.',
);
