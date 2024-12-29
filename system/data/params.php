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

class params
{
    // Скидки / Наценки
    public static $disconunt = [
        'service' => [
            'time' => [
                'buy' => [
                    '15' => '+:30',
                    '90' => '-:5%',
                    '180' => '-:7%',
                ],

                'extend' => [
                    '1' => '+:100%',
                    '15' => '+:30%',
                    '90' => '-:5%',
                    '180' => '-:7%',
                ],
            ],

            9 => [
                'buy' => [
                    '30' => '+:49%',
                ],

                'extend' => [
                    '1' => '+:50%',
                ],
            ],
        ],
    ];

    // Стандартные порты
    public static $aDefPort = [
        'cs' => 27015,
        'cssold' => 27015,
        'css' => 27015,
        'csgo' => 27015,
        'cs2' => 27015,
        'rust' => 28015,
        'samp' => 7777,
        'crmp' => 7777,
        'mta' => 22003,
        'mc' => 25565,
    ];

    // Параметры раздела "Копии"
    public static $section_copy = [
        'cs' => [
            'CopyFull' => 'cstrike',

            'aCopy' => [
                'addons' => 'Директория с дополнениями (модули/плагины)',
                'cfg' => 'Файлы настроек (server.cfg/motd.txt/liblist.gam/banned.cfg и т.д.)',
                'dlls' => 'Директория с библиотеками (название.so)',
                'gfx' => 'Директория с изображениями (название.tga)',
                'maps' => 'Директория с картами (название.bsp)',
                'models' => 'Директория с моделями (название.mdl)',
                'sound' => 'Директория со звуками (название.mp3/название.wav)',
            ],

            'aCopyDir' => [
                'addons' => 'cstrike/addons',
                'dlls' => 'cstrike/dlls',
                'gfx' => 'cstrike/gfx',
                'maps' => 'cstrike/maps',
                'models' => 'cstrike/models',
                'sound' => 'cstrike/sound',
            ],

            'aCopyFile' => [
                'cfg' => 'cstrike/server.cfg cstrike/motd.txt cstrike/fastdl.cfg cstrike/liblist.gam cstrike/banned.cfg cstrike/listip.cfg cstrike/reunion.cfg cstrike/mapcycle.txt',
            ],
        ],

        'cssold' => [
            'CopyFull' => 'cstrike',

            'aCopy' => [
                'addons' => 'Директория с дополнениями (модули/плагины)',
                'cfg' => 'Файлы настроек (server.cfg/motd.txt/banned_user.cfg/banned_ip.cfg и т.д.)',
                'maps' => 'Директория с картами (название.bsp)',
                'models' => 'Директория с моделями (название.mdl)',
                'sound' => 'Директория со звуками (название.mp3/название.wav)',
            ],

            'aCopyDir' => [
                'addons' => 'cstrike/addons',
                'maps' => 'cstrike/maps',
                'models' => 'cstrike/models',
                'sound' => 'cstrike/sound',
            ],

            'aCopyFile' => [
                'cfg' => 'cstrike/cfg/server.cfg cstrike/motd.txt cstrike/fastdl.cfg cstrike/banned_user.cfg cstrike/banned_ip.cfg cstrike/mapcycle.txt cstrike/maplist.txt',
            ],
        ],

        'css' => [
            'CopyFull' => 'cstrike',

            'aCopy' => [
                'addons' => 'Директория с дополнениями (модули/плагины)',
                'cfg' => 'Файлы настроек (server.cfg/motd.txt/banned_user.cfg/banned_ip.cfg и т.д.)',
                'maps' => 'Директория с картами (название.bsp)',
                'models' => 'Директория с моделями (название.mdl)',
                'sound' => 'Директория со звуками (название.mp3/название.wav)',
            ],

            'aCopyDir' => [
                'addons' => 'cstrike/addons',
                'maps' => 'cstrike/maps',
                'models' => 'cstrike/models',
                'sound' => 'cstrike/sound',
            ],

            'aCopyFile' => [
                'cfg' => 'cstrike/cfg/server.cfg cstrike/motd.txt cstrike/fastdl.cfg cstrike/banned_user.cfg cstrike/banned_ip.cfg cstrike/mapcycle.txt cstrike/maplist.txt',
            ],
        ],

        'csgo' => [
            'CopyFull' => 'csgo',

            'aCopy' => [
                'addons' => 'Директория с дополнениями (модули/плагины)',
                'cfg' => 'Файлы настроек (server.cfg/motd.txt/banned_user.cfg/banned_ip.cfg и т.д.)',
                'maps' => 'Директория с картами (название.bsp)',
                'models' => 'Директория с моделями (название.mdl)',
                'sound' => 'Директория со звуками (название.mp3/название.wav)',
            ],

            'aCopyDir' => [
                'addons' => 'csgo/addons',
                'maps' => 'csgo/maps',
                'models' => 'csgo/models',
                'sound' => 'csgo/sound',
            ],

            'aCopyFile' => [
                'cfg' => 'csgo/cfg/server.cfg csgo/motd.txt csgo/fastdl.cfg csgo/banned_user.cfg csgo/banned_ip.cfg csgo/mapcycle.txt csgo/maplist.txt',
            ],
        ],

        'cs2' => [
            'CopyFull' => 'csgo',

            'aCopy' => [
                'addons' => 'Директория с дополнениями (модули/плагины)',
                'cfg' => 'Файлы настроек (server.cfg/motd.txt/banned_user.cfg/banned_ip.cfg и т.д.)',
                'maps' => 'Директория с картами (название.bsp)',
                'models' => 'Директория с моделями (название.mdl)',
                'sound' => 'Директория со звуками (название.mp3/название.wav)',
            ],

            'aCopyDir' => [
                'addons' => 'csgo/addons',
                'maps' => 'csgo/maps',
                'models' => 'csgo/models',
                'sound' => 'csgo/sound',
            ],

            'aCopyFile' => [
                'cfg' => 'csgo/cfg/server.cfg csgo/motd.txt csgo/fastdl.cfg csgo/banned_user.cfg csgo/banned_ip.cfg csgo/mapcycle.txt csgo/maplist.txt',
            ],
        ],

        'rust' => [
            'CopyFull' => '*',

            'aCopy' => [
                'addons' => 'Директория с дополнениями (модули/плагины)',
                'cfg' => 'Файлы настроек (server.cfg/motd.txt/banned_user.cfg/banned_ip.cfg и т.д.)',
                'maps' => 'Директория с картами (название.bsp)',
                'models' => 'Директория с моделями (название.mdl)',
                'sound' => 'Директория со звуками (название.mp3/название.wav)',
            ],

            'aCopyDir' => [
                'addons' => 'csgo/addons',
                'maps' => 'csgo/maps',
                'models' => 'csgo/models',
                'sound' => 'csgo/sound',
            ],

            'aCopyFile' => [
                'cfg' => 'csgo/cfg/server.cfg csgo/motd.txt csgo/fastdl.cfg csgo/banned_user.cfg csgo/banned_ip.cfg csgo/mapcycle.txt csgo/maplist.txt',
            ],
        ],

        'samp' => [
            'CopyFull' => '*',

            'aCopy' => [
                'filterscripts' => 'Директория с дополнительными скриптами',
                'cfg' => 'Файл настроек (server.cfg)',
                'gamemodes' => 'Директория с модами',
                'scriptfiles' => 'Директория с скриптами',
            ],

            'aCopyDir' => [
                'filterscripts' => 'filterscripts',
                'gamemodes' => 'gamemodes',
                'scriptfiles' => 'scriptfiles',
            ],

            'aCopyFile' => [
                'cfg' => 'server.cfg',
            ],
        ],

        'crmp' => [
            'CopyFull' => '*',

            'aCopy' => [
                'filterscripts' => 'Директория с дополнительными скриптами',
                'cfg' => 'Файл настроек (server.cfg)',
                'gamemodes' => 'Директория с модами',
                'scriptfiles' => 'Директория с скриптами',
            ],

            'aCopyDir' => [
                'filterscripts' => 'filterscripts',
                'gamemodes' => 'gamemodes',
                'scriptfiles' => 'scriptfiles',
            ],

            'aCopyFile' => [
                'cfg' => 'server.cfg',
            ],
        ],

        'mta' => [
            'CopyFull' => '*',

            'aCopy' => [
                'databases' => 'Директория баз данных',
                'cfg' => 'Файлы настроек (mtaserver.conf, acl.xml, vehiclecolors.conf и т.д.)',
                'modules ' => 'Директория с модулями',
                'resources' => 'Директория с ресурсами',
            ],

            'aCopyDir' => [
                'databases' => 'mods/deathmatch/databases',
                'modules' => 'mods/deathmatch/modules',
                'resources' => 'mods/deathmatch/resources',
            ],

            'aCopyFile' => [
                'cfg' => 'mods/deathmatch/mtaserver.conf mods/deathmatch/acl.xml mods/deathmatch/vehiclecolors.conf mods/deathmatch/settings.xml',
            ],
        ],
    ];

    // Дни в месяцах
    public static $aDayMonth = [
        1 => 31, 2 => 28, 3 => 31, 4 => 30,
        5 => 31, 6 => 30, 7 => 31, 8 => 31,
        9 => 30, 10 => 31, 11 => 30, 12 => 31,
    ];

    // Названия месяцев
    public static $aNameMonth = [
        1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
        5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
        9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь',
    ];

    // Директории
    public static $aDirGame = [
        'cs' => 'cstrike',
        'cssold' => 'cstrike',
        'css' => 'cstrike',
        'csgo' => 'csgo',
        'cs2' => 'cs2',
        'rust' => '',
        'samp' => '',
        'crmp' => '',
        'mta' => '',
        'mc' => '',
    ];

    // Исполняемые файлы
    public static $aFileGame = [
        'cs' => 'hlds_run hlds_linux hlds_i686',
        'cssold' => 'srcds_run srcds_i486 srcds_i686',
        'css' => 'srcds_linux srcds_run',
        'csgo' => 'srcds_linux srcds_run',
        'cs2' => 'srcds_linux srcds_run',
        'rust' => 'RustDedicated',
        'samp' => 'samp03svr',
        'crmp' => 'samp03svr-cr',
        'mta' => 'mta-server',
        'mc' => 'start.jar',
    ];
}
