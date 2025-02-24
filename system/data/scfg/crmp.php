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

$aScfg = [
    'hostname' => 'Название игрового сервера.',
    'rcon' => 'Управление RCON командами.',
    'rcon_password' => 'Пароль для упраления сервером через RCON команды.',
    'password' => 'Пароль для входа на сервер.',
    'lanmode' => 'Тип игрового сервера.',
    'gamemode0' => 'Моды на сервере.',
    'filterscripts' => 'Сценарии на сервере.',
    'announce' => 'Отображение сервера в интернете.',
    'weburl' => 'Адрес сайта севера.',
    'maxnpc' => 'Максимальное число NPC подключаемых к серверу.',
    'onfoot_rate' => 'Время в миллисекундах за которое сервер обновляет данные о пешем игроке.',
    'incar_rate' => 'Время в миллисекундах за которое сервер обновляет данные о игроке находящемся в транспорте.',
    'weapon_rate' => 'Время в миллисекундах за которое сервер обновляет данные о стрельбе игрока.',
    'stream_distance' => 'Дистанция для обновления стримера игроков.',
    'stream_rate' => 'Время в миллисекундах за которое сервер обновляет игроков в стримере.',
    'logqueries' => 'Логирование всех запросов.',
];

$aScfg_form = [
    'hostname' => '<input value="[hostname]" name="config[\'hostname\']">',
    'rcon_password' => '<input value="[rcon_password]" name="config[\'rcon_password\']">',
    'password' => '<input value="[password]" name="config[\'password\']">',
    'gamemode0' => '<input value="[gamemode0]" name="config[\'gamemode0\']">',
    'filterscripts' => '<input value="[filterscripts]" name="config[\'filterscripts\']">',
    'maxnpc' => '<input value="[maxnpc]" name="config[\'maxnpc\']">',
    'onfoot_rate' => '<input value="[onfoot_rate]" name="config[\'onfoot_rate\']">',
    'incar_rate' => '<input value="[incar_rate]" name="config[\'incar_rate\']">',
    'weapon_rate' => '<input value="[weapon_rate]" name="config[\'weapon_rate\']">',
    'stream_distance' => '<input value="[stream_distance]" name="config[\'stream_distance\']">',
    'stream_rate' => '<input value="[stream_rate]" name="config[\'stream_rate\']">',
    'lagcompmode' => '<select name="config[\'lagcompmode\']">'
        . '<option value="0"> 0 выкл. компенсацию пинга</option>'
        . '<option value="1"> 1 вкл. компенсацию пинга</option>'
        . '<option value="2"> 2 вкл. компенсацию пинга только для обновления позиций.</option></select>',
    'rcon' => '<select name="config[\'rcon\']">'
        . '<option value="0">Запрещено</option>'
        . '<option value="1">Разрешено</option></select>',
    'announce' => '<select name="config[\'announce\']">'
        . '<option value="0">Выключено</option>'
        . '<option value="1">Включено</option></select>',
    'logqueries' => '<select name="config[\'logqueries\']">'
        . '<option value="0">Выключено</option>'
        . '<option value="1">Включено</option></select>',
    'lanmode' => '<select name="config[\'lanmode\']">'
        . '<option value="0">Интернет</option>'
        . '<option value="1">Локальный</option></select>',
];
