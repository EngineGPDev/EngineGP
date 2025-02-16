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
System::noauth($auth, $go);

setcookie('refresh_token', '', [
    'expires' => $start_point - 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'samesite' => 'Strict',
]);

// Обновление активности
$sql->query('UPDATE `users` set `time`="' . ($start_point - 10) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

System::back($cfg['http']);
