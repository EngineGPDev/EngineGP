<?php

/*
 * Copyright 2018-2024 Solovev Sergei
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$text = [
    'ssh' => [
        // Если вывод ошибок по группе пользователя
        'admin' => 'Не удалось создать соединение с оборудованием.',

        'support' => 'Проблемы на линии связи между панелью и оборудованием.',

        'user' => 'Не удалось выполнить операцию, ошибка #1, обратитесь в тех.поддержку.',

        // Если вывод ошибок не учитывать группу пользователя
        'all' => 'Не удалось создать соединение с оборудованием.',
    ],

    'cpu' => [
        // Если вывод ошибок по группе пользователя
        'admin' => 'Не удается запустить игровой сервер, нет свободного потока.',

        'support' => 'Не удается запустить игрвоой сервер, нет свободного потока.',

        'user' => 'Не удалось выполнить операцию, ошибка #с404, обратитесь в тех.поддержку.',

        // Если вывод ошибок не учитывать группу пользователя
        'all' => 'Не удается запустить игровой сервер, нет свободного потока.',
    ],

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
];
