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
