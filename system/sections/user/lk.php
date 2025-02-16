<?php

/*
 * Copyright 2018-2025 Solovev Sergei
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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Проверка на авторизацию
System::noauth();

// Генерация пароля
if (isset($url['passwd'])) {
    System::out(System::passwd(10));
}

$aTitle = [
    'index' => 'Профиль',
    'settings' => 'Настройки',
    'auth' => 'Логи авторизаций',
    'logs' => 'История операций',
    'security' => 'Безопасность',
];

$url['subsection'] ??= 'index';

// Подключение раздела
if (in_array($url['subsection'], ['index', 'settings', 'auth', 'logs', 'security', 'action', 'cashback'])) {
    $title = $aTitle[$url['subsection']] ?? '';
    $html->nav($title);

    include(LIB . 'users.php');

    users::nav($url['subsection']);

    include(SEC . 'user/lk/' . $url['subsection'] . '.php');
} else {
    include(ENG . '404.php');
}
