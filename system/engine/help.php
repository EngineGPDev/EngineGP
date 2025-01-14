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

// Проверка на авторизацию
sys::noauth();

$sql->query('SELECT `id` FROM `users` WHERE `id`="' . $user['id'] . '" AND `help`="0" LIMIT 1');
if (!$sql->num()) {
    $html->nav('Техническая поддержка');

    $html->get('noaccess', 'sections/help');
    $html->pack('main');
} else {
    // Подключение раздела
    if (!in_array($section, ['create', 'dialog', 'open', 'close', 'notice', 'upload'])) {
        include(ENG . '404.php');
    }

    $aNav = [
        'help' => 'Техническая поддержка',
        'create' => 'Создание вопроса',
        'dialog' => 'Решение вопроса',
        'open' => 'Список открытых вопросов',
        'close' => 'Список закрытых вопросов',
    ];

    $title = $aNav[$section] ?? $section;
    $html->nav($aNav['help'], $cfg['http'] . 'help/section/open');
    $html->nav($title);

    include(SEC . 'help/' . $section . '.php');
}
