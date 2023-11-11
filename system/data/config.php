<?php
	if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$cfg = array(
		'name' => 'EngineGP', // Название сайта
		'graph' => 'EngineGP', // Описание сайта
		'url' => 'IPADDR', // Тут ваш IP или Домен, пример: 127.0.0.1
		'http' => 'http://IPADDR/', // Тут ваш IP или Домен с указанием http, пример: http://127.0.0.1
		'plugins' => 'http://IPADDR/files/plugins/', // Путь к плагинам
		'ip' => 'IPADDR', // IP-Адрес сайта прмер: 127.0.0.1
		'subnet' => 'IPADDR.0/23', // Подсеть сайта пример: 127.0.0.1.0/23 .0/23(не стирать)

		'cdn' => 'http://cdn.enginegp.ru/', // CDN сервис EGP, если ваш сайт переехал на https то следует тут тоже сменить протокол с http на https

		// Данные для пополнения баланса пользователям
		'freekassa_id' => '', // Номер кассы
		'freekassa_key_1' => '', // Первый секретный ключ FreeKassa
		'freekassa_key_2' => '', // Второй секретный ключ FreeKassa
		'webmoney_wmr' => '', // Wmr кошелек
		'webmoney_key' => '', // Секретный ключ WebMoney
		'unitpay_key' => '', // Секретный ключ UnitPay
		'unitpay_pubkey' => '', // Публичный ключ UnitPay

		// Данные для отправки почты
		'smtp_url' => '', // * SMTP URL, пример: ssl://smtp.mail.ru | ssl://smtp.yandex.ru | ssl://smtp.google.com
		'smtp_login' => '', // * E-mail отправителя, пример: support@enginegp.ru
		'smtp_passwd' => '', // * Пароль от E-mail отправителя support@enginegp.ru
		'smtp_name' => '', // * Имя отправителя, пример: EngineGP Support
		'smtp_mail' => '', // * E-mail отправителя(ещё раз) - (support@enginegp.ru)

		// Уведомления о сообщениях в тикетах
		'notice_admin' => array(1), // перечислить id пользователя, на их почты будут отправлять уведомления

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
		'cron_key' => 'CRONKEY', // Ключ для cron.php
		'cron_taskset' => '0', // Ядро, на котором запускать cron.php (уставновить отличный от нуля, если на VDS больше 1 ядра/потока)

		// Кеш (кол-во секунд)
		'mcache_server_mon' => 2, // Мониторинг (онлайн, название, карта)
		'mcache_server_status' => 2, // Статус (состояние: включен, смена карты, переустановка)
		'mcache_server_resources' => 2, // Ресурсы (нагрузка: cpu, ram hdd)

		// Контролеры / параметры
		'autostop' => 30, // Через сколько минут выключить CW сервер, если на нем нет игроков
		'teststop' => 10, // Через сколько минут выключить тестовый сервер, если на нем нет игроков
		'fpsplus' => 100, // Прибавление fps к параметру "sys_ticrate" при старте (пример: 1000+100 | итог: sys_ticrate 1100)
		'pingboost' => 3, // Значение pingboost по умолчанию (1,2,3 | 0 = без pingboost)
		'change_pingboost' => false, // Смена pingboost клиентом (true = разрешено / false = запрещено)
		'cpu_route' => true, // Активация (EngineGP CPU ROUTE) автораспределения игровых серверов по ядрам/потокам | false - выключена
		'cpu_first' => true, // Запретить использовать первый поток cpu (значение имеет вес при 2х и более потоках) | РЕКОМЕНДУЕМОЕ ЗНАЧЕНИЕ: true
		'crontabs' => 3, // Количество заданий crontab на 1 игровой сервер

		// Услуга "Контроль"
		'control_delete' => 10, // через сколько дней удалять услугу
		'control_time' => array(30, 90), // периоды аренды
		'control_limit' => array( // лимит / цена
			1 => 50,
			5 => 100,
			10 => 150,
			20 => 250
		),
		'control_server' => 'http://games.enginegp.ru/', // головной сервер, путь к архивам
		'control_packs' => array(
			'cs' => array(
				'6153' => 'Build 6153',
				'5787' => 'Build 5787',
				'rehlds' => 'ReHLDS'
			),
			'cssold' => array(
				'cssold' => 'Стандарт',
			),
			'css' => array(
				'default' => 'Стандарт',
			),
			'csgo' => array(
				'default' => 'Стандарт',
			)
		),
		'control_install' => array(
			'cs' => 'pack="6153", `map_start`="de_dust2", `fps`="1100"',
			'cssold' => 'pack="cssold", `map_start`="de_dust2", `fps`="1000",`tickrate`="100"',
			'css' => 'pack="default", `map_start`="de_dust2", `tickrate`="100"',
			'csgo' => 'pack="default", `map_start`="de_dust2", `tickrate`="128"'
		),
		'control_steamcmd' => array(
			'css' => 232330,
			'csgo' => 740
		),

		// Задержка в возможности повторной переустановки сервера (минуты)
		'reinstall' => array(
			'cs' => '60',
			'cssold' => '60',
			'css' => '60',
			'csgo' => '60',
			'samp' => '60',
			'crmp' => '60',
			'mta' => '60',
			'mc' => '60'
		),

		// Задержка в возможности повторного обновления сервера (минуты)
		'update' => array(
			'cs' => '60',
			'cssold' => '60',
			'css' => '60',
			'csgo' => '60'
		),

		// RAM на 1 слот
		'ram' => array(
			'cs' => '32',
			'cssold' => '32',
			'css' => '32',
			'csgo' => '32',
			'samp' => '32',
			'crmp' => '32',
			'mta' => '32'
		),

		// Максимальная нагрузка % на RAM 1-го игрового сервера
		'ram_use_max' => array(
			'cs' => '99',
			'cssold' => '99',
			'css' => '99',
			'csgo' => '99',
			'samp' => '99',
			'crmp' => '99',
			'mta' => '99',
		),

		// Максимальная нагрузка % на CPU 1-го игрового сервера
		'cpu_use_max' => array(
			'cs' => '99',
			'cssold' => '99',
			'css' => '99',
			'csgo' => '99',
			'samp' => '99',
			'crmp' => '99',
			'mta' => '99',
			'mc' => '99',
		),

		// Параметры ftp игровых серверов
		'ftp' => array(
			// Корневая директория серверов (false = server/(cstrike | csgo | l4d)/ | true = server/)
			'root' => array(
				'cs' => false,
				'cssold' => false,
				'css' => false,
				'csgo' => false,
				'samp' => false,
				'crmp' => false,
				'mta' => false,
				'mc' => false
			),

			// Путь к директори для файлового менеджера EngineGP (используется при $cfg['ftp']['root'][$game] == TRUE)
			'dir' => array(
				'cs' => '/cstrike',
				'cssold' => '/cstrike',
				'css' => '/cstrike',
				'csgo' => '/csgo',
				'samp' => '/',
				'crmp' => '/',
				'mta' => '/mods/deathmatch',
				'mc' => '/'
			),

			// Путь к директори (используется при $cfg['ftp']['root'][$game] == FALSE)
			'home' => array(
				'cs' => '/cstrike',
				'cssold' => '/cstrike',
				'css' => '/cstrike',
				'csgo' => '/csgo',
				'samp' => '/',
				'crmp' => '/',
				'mta' => '/mods/deathmatch',
				'mc' => '/'
			),
		),

		// Смена количества слот
		'change_slots' => array(
			'cs' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'cssold' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'css' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'csgo' => array(
				'days' => false, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'samp' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'crmp' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'mta' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			),

			'mc' => array(
				'days' => true, // При смене кол-ва слот вычитывать/добавлять дни аренды (add == true), если false, то смена (увеличение) на платной основе
				'down' => true, // Если 'days == true', то дать возможность уменьшать слоты
				'add' => true // Если 'down == true', то при уменьшении кол-во слот, добавлять дни к аренде
			)
		),

		// Смена локации
		'change_unit' => array(
			'cs' => false,
			'cssold' => false,
			'css' => false,
			'csgo' => false,
			'samp' => false,
			'crmp' => false,
			'mta' => false,
			'mc' => false
		),

		// Аренда выделенного адреса (пример: cs 1.6 => ip:27015)
		'buy_address' => array(
			'cs' => true, // true == по месячная аренда, false == разовая оплата на весь период аренды игрового сервера
			'cssold' => true,
			'css' => true,
			'csgo' => true,
			'samp' => true,
			'crmp' => true,
			'mta' => true,
			'mc' => true
		),

		// Тестовый период (тестовый период возможен только пользователям с подтвержденным номером, выдается 1 раз)
		'tests' => array(
			'game' => false, // Разрешить брать тест другой игры
			'sametime' => false // Разрешить брать тест другой игры во время тестирования. (при условии, что game == true)
		),

		// Боты на сервере (включение -nobots в параметре запуска сервера)
		'bots' => array(
			'cssold' => false, // true == разрешены, false == запрещены
			'css' => false,
			'csgo' => false
		)
	);
?>