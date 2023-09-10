<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$cfg = [
    // Название сайта
    'name' => 'EngineGP',
    // Описание сайта
    'graph' => 'EngineGP',
    // Тут ваш IP или Домен, пример: 127.0.0.1
    'url' => 'IPADDR',
    // Тут ваш IP или Домен с указанием http, пример: http://127.0.0.1
    'http' => 'http://IPADDR/',
    // Путь к плагинам
    'plugins' => 'http://IPADDR/files/plugins/',
    // IP-Адрес сайта прмер: 127.0.0.1
    'ip' => 'IPADDR',
    // Подсеть сайта пример: 127.0.0.1.0/23 .0/23(не стирать)
    'subnet' => 'IPADDR.0/23',
    // CDN сервис EGP, если ваш сайт переехал на https то следует тут тоже сменить протокол с http на https
    'cdn' => 'http://cdn.enginegp.ru/',
    // Данные для пополнения баланса пользователям
    'freekassa_id' => '',
    // Номер кассы
    'freekassa_key_1' => '',
    // Первый секретный ключ FreeKassa
    'freekassa_key_2' => '',
    // Второй секретный ключ FreeKassa
    'webmoney_wmr' => '',
    // Wmr кошелек
    'webmoney_key' => '',
    // Секретный ключ WebMoney
    'unitpay_key' => '',
    // Секретный ключ UnitPay
    'unitpay_pubkey' => '',
    // Публичный ключ UnitPay
    // Данные для отправки почты
    'smtp_url' => '',
    // SMTP URL, пример: ssl://smtp.mail.ru | ssl://smtp.yandex.ru | ssl://smtp.google.com
    'smtp_login' => '',
    // E-mail отправителя, пример: support@enginegp.ru
    'smtp_passwd' => '',
    // Пароль от E-mail отправителя support@enginegp.ru
    'smtp_name' => '',
    // Имя отправителя, пример: EngineGP Support
    'smtp_mail' => '',
    // E-mail отправителя(ещё раз) - (support@enginegp.ru)
    // Уведомления о сообщениях в тикетах
    'notice_admin' => [1],
    // перечислить id пользователя, на их почты будут отправлять уведомления
    // Данные для работы с sms шлюзом (sms.ru)
    // шлюз
    'sms_gateway' => 'http://sms.ru/sms/send?api_id=_KEY_&from=_WHO_',
    // GET параметр для получателя
    'sms_to' => 'to',
    // GET параметр для сообщения
    'sms_text' => 'text',
    // значение удачи
    'sms_ok' => '100',
    // Новости
    // Кол-во новостей на странице
    'news_page' => 10,
    // Данные для рассчетов
    // Включена ли система контроля скидок (запрет выгоды при использовании скидок)
    'benefitblock' => true,
    // Включена ли система расчетного периода
    'settlement_period' => false,
    // Наименование валюты
    'currency' => 'руб.',
    // Курс к рублю
    'curinrub' => 1,
    // Суммировать скидки промо-кода и периода
    'promo_discount' => true,
    // Процент партнерской программы
    'part_proc' => 10,
    // true == зачислять в отдельный счет / false == сразу суммировать с балансом
    'part_money' => true,
    // true == пополнять процент при пополнении счета рефералом / false == пополнять при списании средств у реферала (продление/аренда)
    'part' => false,
    // Данные для вывода средств (если part_money == true)
    // название кошелька при выводе на баланс сайта
    'part_log' => 'Лицевой счет',
    // процент комисси за вывод
    'part_output_proc' => 10,
    // мин. лимит на вывод за раз пользователем
    'part_limit_min' => 100,
    // макс. лимит на вывод за день пользователем
    'part_limit_max' => 500,
    // лимит на вывод за день
    'part_limit_day' => 5000,
    // true == выводить без одобрения / false == вывод производиться после одобрения администратором
    'part_output' => false,
    // платежный шлюз для вывода (unitpay)
    'part_gateway' => 'unitpay',
    // Остальное
    // Вывод сообщений (ошибок) по группе пользователя
    'text_group' => true,
    // Требовать повторного ввода капчи (false - сохранить удачу в кеше на 60 секунд)
    'recaptcha' => false,
    // Через сколько дней удалить игровой сервер после окончания его аренды.
    'server_delete' => 3,
    // Путь к steamcmd на локациях (/path/steam)
    'steamcmd' => '/path/cmd',
    // Файл правил для FireWall (блокировка на уровне оборудования) (/root/_FILE_)
    'iptables' => 'iptables_block',
    // Ключ для cron.php
    'cron_key' => 'CRONKEY',
    // Ядро, на котором запускать cron.php (уставновить отличный от нуля, если на VDS больше 1 ядра/потока)
    'cron_taskset' => '0',
    // Lax, None, Strict | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
    'cookie_same_site' => 'Lax',
    // Кеш (кол-во секунд)
    // Мониторинг (онлайн, название, карта)
    'mcache_server_mon' => 2,
    // Статус (состояние: включен, смена карты, переустановка)
    'mcache_server_status' => 2,
    // Ресурсы (нагрузка: cpu, ram hdd)
    'mcache_server_resources' => 2,
    // Контролеры / параметры
    // Через сколько минут выключить CW сервер, если на нем нет игроков
    'autostop' => 30,
    // Через сколько минут выключить тестовый сервер, если на нем нет игроков
    'teststop' => 10,
    // Прибавление fps к параметру "sys_ticrate" при старте (пример: 1000+100 | итог: sys_ticrate 1100)
    'fpsplus' => 100,
    // Значение pingboost по умолчанию (1,2,3 | 0 = без pingboost)
    'pingboost' => 3,
    // Смена pingboost клиентом (true = разрешено / false = запрещено)
    'change_pingboost' => false,
    // Активация (EngineGP CPU ROUTE) автораспределения игровых серверов по ядрам/потокам | false - выключена
    'cpu_route' => true,
    // Запретить использовать первый поток cpu (значение имеет вес при 2х и более потоках) | РЕКОМЕНДУЕМОЕ ЗНАЧЕНИЕ: true
    'cpu_first' => true,
    // Количество заданий crontab на 1 игровой сервер
    'crontabs' => 3,
    // Услуга "Контроль"
    // через сколько дней удалять услугу
    'control_delete' => 10,
    // периоды аренды
    'control_time' => [30, 90],
    // лимит / цена
    'control_limit' => [
        1 => 50,
        5 => 100,
        10 => 150,
        20 => 250,
    ],
    // головной сервер, путь к архивам
    'control_server' => 'http://games.enginegp.ru/',
    // Задержка в возможности повторной переустановки сервера (минуты)
    'control_packs' => ['cs' => ['6153' => 'Build 6153', '5787' => 'Build 5787', 'rehlds' => 'ReHLDS'], 'cssold' => ['cssold' => 'Стандарт'], 'css' => ['default' => 'Стандарт'], 'csgo' => ['default' => 'Стандарт']],
    'control_install' => ['cs' => 'pack="6153", `map_start`="de_dust2", `fps`="1100"', 'cssold' => 'pack="cssold", `map_start`="de_dust2", `fps`="1000",`tickrate`="100"', 'css' => 'pack="default", `map_start`="de_dust2", `tickrate`="100"', 'csgo' => 'pack="default", `map_start`="de_dust2", `tickrate`="128"'],
    'control_steamcmd' => ['css' => 232330, 'csgo' => 740],
    // Задержка в возможности повторного обновления сервера (минуты)
    'reinstall' => ['cs' => '60', 'cssold' => '60', 'css' => '60', 'csgo' => '60', 'samp' => '60', 'crmp' => '60', 'mta' => '60', 'mc' => '60'],
    'update' => ['cs' => '60', 'cssold' => '60', 'css' => '60', 'csgo' => '60'],
    // RAM на 1 слот
    'ram' => ['cs' => '32', 'cssold' => '32', 'css' => '32', 'csgo' => '32', 'samp' => '32', 'crmp' => '32', 'mta' => '32'],
    // Максимальная нагрузка % на RAM 1-го игрового сервера
    'ram_use_max' => ['cs' => '99', 'cssold' => '99', 'css' => '99', 'csgo' => '99', 'samp' => '99', 'crmp' => '99', 'mta' => '99'],
    // Максимальная нагрузка % на CPU 1-го игрового сервера
    'cpu_use_max' => ['cs' => '99', 'cssold' => '99', 'css' => '99', 'csgo' => '99', 'samp' => '99', 'crmp' => '99', 'mta' => '99', 'mc' => '99'],
    // Параметры ftp игровых серверов
    // Корневая директория серверов (false = server/(cstrike | csgo | l4d)/ | true = server/)
    'ftp' => [
        // Путь к директори для файлового менеджера EngineGP (используется при $cfg['ftp']['root'][$game] == TRUE)
        'root' => ['cs' => false, 'cssold' => false, 'css' => false, 'csgo' => false, 'samp' => false, 'crmp' => false, 'mta' => false, 'mc' => false],
        // Путь к директори (используется при $cfg['ftp']['root'][$game] == FALSE)
        'dir' => ['cs' => '/cstrike', 'cssold' => '/cstrike', 'css' => '/cstrike', 'csgo' => '/csgo', 'samp' => '/', 'crmp' => '/', 'mta' => '/mods/deathmatch', 'mc' => '/'],
        'home' => ['cs' => '/cstrike', 'cssold' => '/cstrike', 'css' => '/cstrike', 'csgo' => '/csgo', 'samp' => '/', 'crmp' => '/', 'mta' => '/mods/deathmatch', 'mc' => '/'],
    ],
    // Смена количества слот
    // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
    // Если 'days == true', то дать возможность уменьшать слоты
    'change_slots' => ['cs' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ], 'cssold' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ], 'css' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ], 'csgo' => [
        'days' => false,
        'down' => true,
        'add' => true,
    ], 'samp' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ], 'crmp' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ], 'mta' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ], 'mc' => [
        'days' => true,
        'down' => true,
        'add' => true,
    ]],
    // Смена локации
    'change_unit' => ['cs' => false, 'cssold' => false, 'css' => false, 'csgo' => false, 'samp' => false, 'crmp' => false, 'mta' => false, 'mc' => false],
    // Аренда выделенного адреса (пример: cs 1.6 => ip:27015)
    // true == по месячная аренда, false == разовая оплата на весь период аренды игрового сервера
    'buy_address' => [
        'cs' => true,
        'cssold' => true,
        'css' => true,
        'csgo' => true,
        'samp' => true,
        'crmp' => true,
        'mta' => true,
        'mc' => true,
    ],
    // Тестовый период (тестовый период возможен только пользователям с подтвержденным номером, выдается 1 раз)
    // Разрешить брать тест другой игры
    'tests' => [
        'game' => false,
        'sametime' => false,
    ],
    // Боты на сервере (включение -nobots в параметре запуска сервера)
    // true == разрешены, false == запрещены
    'bots' => [
        'cssold' => false,
        'css' => false,
        'csgo' => false,
    ],
];
