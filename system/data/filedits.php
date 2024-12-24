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

// Массив файлов для редактирования (раздел "настройки")
$aEdits = [
    'cs' => [
        'all' => [
            'files' => [
                'autoexec.cfg',
                'fastdl.cfg',
                'plugins.ini',
                'users.ini',
                'motd.txt',
            ],
            'path' => [
                'autoexec.cfg' => 'cstrike/',
                'fastdl.cfg' => 'cstrike/',
                'plugins.ini' => 'cstrike/addons/amxmodx/configs/',
                'users.ini' => 'cstrike/addons/amxmodx/configs/',
                'motd.txt' => 'cstrike/',
            ],
            'desc' => [
                'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
                'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
                'plugins.ini' => 'Список плагинов на сервере.',
                'users.ini' => 'Список админов на сервере.',
                'motd.txt' => 'Окно приветствия на сервере.',
            ],
        ],

    ],

    'cssold' => [
        'all' => [
            'files' => [
                'autoexec.cfg',
                'fastdl.cfg',
                'admins_simple.ini',
            ],
            'path' => [
                'autoexec.cfg' => 'cstrike/cfg/',
                'fastdl.cfg' => 'cstrike/cfg/',
                'admins_simple.ini' => 'cstrike/addons/sourcemod/configs/',
            ],
            'desc' => [
                'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
                'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
                'admins_simple.ini' => 'Список админов на сервере.',
            ],
        ],
    ],

    'css' => [
        'all' => [
            'files' => [
                'autoexec.cfg',
                'fastdl.cfg',
                'admins_simple.ini',
            ],
            'path' => [
                'autoexec.cfg' => 'cstrike/cfg/',
                'fastdl.cfg' => 'cstrike/cfg/',
                'admins_simple.ini' => 'cstrike/addons/sourcemod/configs/',
            ],
            'desc' => [
                'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
                'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
                'admins_simple.ini' => 'Список админов на сервере.',
            ],
        ],
    ],

    'csgo' => [
        'all' => [
            'files' => [
                'autoexec.cfg',
                'fastdl.cfg',
                'webapi_authkey.txt',
            ],
            'path' => [
                'autoexec.cfg' => 'csgo/cfg/',
                'fastdl.cfg' => 'csgo/cfg/',
                'webapi_authkey.txt' => 'csgo/',
            ],
            'desc' => [
                'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
                'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
                'webapi_authkey.txt' => 'API ключ для установки карт из мастерской <u>WorkShop</u>.',
            ],
        ],
    ],

    'cs2' => [
        'all' => [
            'files' => [
                'autoexec.cfg',
                'fastdl.cfg',
                'webapi_authkey.txt',
            ],
            'path' => [
                'autoexec.cfg' => 'csgo/cfg/',
                'fastdl.cfg' => 'csgo/cfg/',
                'webapi_authkey.txt' => 'csgo/',
            ],
            'desc' => [
                'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
                'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
                'webapi_authkey.txt' => 'API ключ для установки карт из мастерской <u>WorkShop</u>.',
            ],
        ],
    ],

    'rust' => [
        'all' => [
            'files' => [
                'autoexec.cfg',
                'fastdl.cfg',
                'webapi_authkey.txt',
            ],
            'path' => [
                'autoexec.cfg' => 'csgo/cfg/',
                'fastdl.cfg' => 'csgo/cfg/',
                'webapi_authkey.txt' => 'csgo/',
            ],
            'desc' => [
                'autoexec.cfg' => 'Автоподключаемый конфигурационный файл.',
                'fastdl.cfg' => 'Быстрая закачка файлов с сервера.',
                'webapi_authkey.txt' => 'API ключ для установки карт из мастерской <u>WorkShop</u>.',
            ],
        ],
    ],

    'mta' => [
        'all' => [
            'files' => [
                'mtaserver.conf',
                'acl.xml',
                'vehiclecolors.conf',
            ],
            'path' => [
                'mtaserver.conf' => 'mods/deathmatch/',
                'acl.xml' => 'mods/deathmatch/',
                'vehiclecolors.conf' => 'mods/deathmatch/',
            ],
            'desc' => [
                'mtaserver.conf' => 'Основной конфигурационный файл сервера.',
                'acl.xml' => 'Настройки прав на игровом сервере.',
                'vehiclecolors.conf' => 'Настройки цветов автомобилей на игровом сервере.',
            ],
        ],
    ],

    'mc' => [
        'all' => [
            'files' => [
                'ops.txt',
                'permissions.yml',
                'white-list.txt',
                'banned-players.txt',
                'banned-ips.txt',
            ],
            'path' => [
                'ops.txt' => '/',
                'permissions.yml' => '/',
                'white-list.txt' => '/',
                'banned-players.txt' => '/',
                'banned-ips.txt' => '/',
            ],
            'desc' => [
                'ops.txt' => 'Файл в котором прописываются админы.',
                'permissions.yml' => 'Список разрешений',
                'white-list.txt' => 'Белый список игроков.',
                'banned-players.txt' => 'Забаненные игроки.',
                'banned-ips.txt' => 'Забаненные IP адреса.',
            ],
        ],
    ],
];

if (isset($aEditslist)) {
    // Генерация общего списка редактируемых файлов
    if (isset($aEdits[$server['game']]['all']['files'])) {
        foreach ($aEdits[$server['game']]['all']['files'] as $file) {
            $html->get('edits_list', 'sections/servers/games/settings');
            $html->set('id', $id);
            $html->set('name', $file);
            $html->set('desc', $aEdits[$server['game']]['all']['desc'][$file]);
            $html->pack('edits');
        }
    }

    // Генерация списка редактируемых файлов по тарифу
    if (isset($aEdits[$server['game']][$tarif['name']]['files'])) {
        foreach ($aEdits[$server['game']][$tarif['name']]['files'] as $file) {
            $html->get('edits_list', 'sections/servers/games/settings');
            $html->set('id', $id);
            $html->set('name', $file);
            $html->set('desc', $aEdits[$server['game']][$tarif['name']]['desc'][$file]);
            $html->pack('edits');
        }
    }
}
