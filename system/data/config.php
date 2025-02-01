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

use Symfony\Component\Dotenv\Dotenv;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Загружаем .env
$dotenv = new Dotenv();
$dotenv->load(ROOT.'.env');

$cfg = [
    'name' => $_ENV['APP_NAME'],
    'graph' => $_ENV['APP_NAME'],
    'url' => $_ENV['APP_URL'],
    'http' => $_ENV['APP_PROTOCOL'] . $_ENV['APP_URL'] . '/',
    'plugins' => $_ENV['APP_PROTOCOL'] . $_ENV['APP_URL'] . '/files/plugins/',
    'ip' => $_ENV['APP_IP'],
    'subnet' => $_ENV['APP_IP'],

    // Whoops
    'whoops' => [
        'blacklist' => [
            '_COOKIE' => array_keys($_COOKIE),
            '_SERVER' => array_keys($_SERVER),
            '_ENV' => array_keys($_ENV),
        ],
    ],

    // Данные для пополнения баланса пользователям
    'freekassa_id' => '', // Номер кассы
    'freekassa_key_1' => '', // Первый секретный ключ FreeKassa
    'freekassa_key_2' => '', // Второй секретный ключ FreeKassa
    'webmoney_wmr' => '', // Wmr кошелек
    'webmoney_key' => '', // Секретный ключ WebMoney
    'unitpay_key' => '', // Секретный ключ UnitPay
    'unitpay_pubkey' => '', // Публичный ключ UnitPay

    // Уведомления о сообщениях в тикетах
    'notice_admin' => [1], // перечислить id пользователя, на их почты будут отправлять уведомления

    // Данные для работы с sms шлюзом (sms.ru)
    'sms_gateway' => 'http://sms.ru/sms/send?api_id=_KEY_&from=_WHO_', // шлюз
    'sms_to' => 'to', // GET параметр для получателя
    'sms_text' => 'text', // GET параметр для сообщения
    'sms_ok' => '100', // значение удачи

    // Новости
    'news_page' => 10, // Кол-во новостей на странице

    // Данные для рассчетов
    'benefitblock' => true, // Включена ли система контроля скидок (запрет выгоды при использовании скидок)
    'settlement_period' => false, // Включена ли система расчетного периода
    'currency' => 'руб.', // Наименование валюты
    'curinrub' => 1, // Курс к рублю
    'promo_discount' => true, // Суммировать скидки промо-кода и периода
    'part_proc' => 10, // Процент партнерской программы
    'part_money' => true, // true == зачислять в отдельный счет / false == сразу суммировать с балансом
    'part' => false, // true == пополнять процент при пополнении счета рефералом / false == пополнять при списании средств у реферала (продление/аренда)

    // Данные для вывода средств (если part_money == true)
    'part_log' => 'Лицевой счет', // название кошелька при выводе на баланс сайта
    'part_output_proc' => 10, // процент комисси за вывод
    'part_limit_min' => 100, // мин. лимит на вывод за раз пользователем
    'part_limit_max' => 500, // макс. лимит на вывод за день пользователем
    'part_limit_day' => 5000, // лимит на вывод за день
    'part_output' => false, // true == выводить без одобрения / false == вывод производиться после одобрения администратором
    'part_gateway' => 'unitpay', // платежный шлюз для вывода (unitpay)

    // Остальное
    'text_group' => true, // Вывод сообщений (ошибок) по группе пользователя
    'recaptcha' => false, // Требовать повторного ввода капчи (false - сохранить удачу в кеше на 60 секунд)
    'server_delete' => 3, // Через сколько дней удалить игровой сервер после окончания его аренды.
    'steamcmd' => '/path/cmd', // Путь к steamcmd на локациях (/path/steam)
    'iptables' => 'iptables_block', // Файл правил для FireWall (блокировка на уровне оборудования) (/root/_FILE_)
    'cron_key' => $_ENV['APP_CRONKEY'], // Ключ для cron.php

    // Кеш (кол-во секунд)
    'mcache_server_mon' => 60, // Мониторинг (онлайн, название, карта)
    'mcache_server_status' => 60, // Статус (состояние: включен, смена карты, переустановка)
    'mcache_server_resources' => 60, // Ресурсы (нагрузка: cpu, ram hdd)

    // Контролеры / параметры
    'autostop' => 30, // Через сколько минут выключить CW сервер, если на нем нет игроков
    'teststop' => 10, // Через сколько минут выключить тестовый сервер, если на нем нет игроков
    'fpsplus' => 100, // Прибавление fps к параметру "sys_ticrate" при старте (пример: 1000+100 | итог: sys_ticrate 1100)
    'pingboost' => 3, // Значение pingboost по умолчанию (1,2,3 | 0 = без pingboost)
    'change_pingboost' => false, // Смена pingboost клиентом (true = разрешено / false = запрещено)
    'crontabs' => 3, // Количество заданий crontab на 1 игровой сервер

    // Задержка в возможности повторной переустановки сервера (минуты)
    'reinstall' => [
        'cs' => '60',
        'cssold' => '60',
        'css' => '60',
        'csgo' => '60',
        'cs2' => '60',
        'rust' => '60',
        'samp' => '60',
        'crmp' => '60',
        'mta' => '60',
        'mc' => '60',
    ],

    // Задержка в возможности повторного обновления сервера (минуты)
    'update' => [
        'cs' => '60',
        'cssold' => '60',
        'css' => '60',
        'csgo' => '60',
        'cs2' => '60',
        'rust' => '60',
    ],

    // Параметры ftp игровых серверов
    'ftp' => [
        // Корневая директория серверов (false = server/(cstrike | csgo | l4d)/ | true = server/)
        'root' => [
            'cs' => false,
            'cssold' => false,
            'css' => false,
            'csgo' => false,
            'cs2' => false,
            'rust' => false,
            'samp' => false,
            'crmp' => false,
            'mta' => false,
            'mc' => false,
        ],

        // Путь к директори для файлового менеджера EngineGP (используется при $cfg['ftp']['root'][$game] == TRUE)
        'dir' => [
            'cs' => '/cstrike',
            'cssold' => '/cstrike',
            'css' => '/cstrike',
            'csgo' => '/csgo',
            'cs2' => '/csgo',
            'rust' => '/',
            'samp' => '/',
            'crmp' => '/',
            'mta' => '/mods/deathmatch',
            'mc' => '/',
        ],

        // Путь к директори (используется при $cfg['ftp']['root'][$game] == FALSE)
        'home' => [
            'cs' => '/cstrike',
            'cssold' => '/cstrike',
            'css' => '/cstrike',
            'csgo' => '/csgo',
            'cs2' => '/game',
            'rust' => '/',
            'samp' => '/',
            'crmp' => '/',
            'mta' => '/mods/deathmatch',
            'mc' => '/',
        ],
    ],

    // Смена количества слот
    'change_slots' => [
        'cs' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'cssold' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'css' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'csgo' => [
            'days' => false, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'cs2' => [
            'days' => false, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'rust' => [
            'days' => false, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'samp' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'crmp' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'mta' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],

        'mc' => [
            'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
            'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
            'add' => true, // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
        ],
    ],

    // Смена локации
    'change_unit' => [
        'cs' => false,
        'cssold' => false,
        'css' => false,
        'csgo' => false,
        'cs2' => false,
        'rust' => false,
        'samp' => false,
        'crmp' => false,
        'mta' => false,
        'mc' => false,
    ],

    // Аренда выделенного адреса (пример: cs 1.6 => ip:27015)
    'buy_address' => [
        'cs' => true, // true == по месячная аренда, false == разовая оплата на весь период аренды игрового сервера
        'cssold' => true,
        'css' => true,
        'csgo' => true,
        'cs2' => true,
        'rust' => true,
        'samp' => true,
        'crmp' => true,
        'mta' => true,
        'mc' => true,
    ],

    // Тестовый период (тестовый период возможен только пользователям с подтвержденным номером, выдается 1 раз)
    'tests' => [
        'game' => false, // Разрешить брать тест другой игры
        'sametime' => false, // Разрешить брать тест другой игры во время тестирования. (при условии, что game == true)
    ],

    // Боты на сервере (включение -nobots в параметре запуска сервера)
    'bots' => [
        'cssold' => false, // true == разрешены, false == запрещены
        'css' => false,
        'csgo' => false,
        'cs2' => false,
    ],
];
