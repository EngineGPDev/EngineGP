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

date_default_timezone_set('Europe/Moscow');

// Composer
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Please install composer and run composer install' . PHP_EOL);
}
require(__DIR__ . '/vendor/autoload.php');

// Загружаем .env
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

if ($_ENV['RUN_MODE'] === 'dev') {
    // Включение отображения ошибок в режиме разработки
    ini_set('display_errors', true);
    ini_set('html_errors', true);
    ini_set('error_reporting', E_ALL);
} else {
    // Отключение отображения ошибок в продакшене
    ini_set('display_errors', false);
    ini_set('html_errors', false);
    ini_set('error_reporting', 0);
}

define('EGP', true);
define('DIR', __DIR__);
define('ROOT', DIR . '/');
define('SYS', ROOT . 'system/');
define('TPL', ROOT . 'template/');
define('TEMP', ROOT . 'temp/');
define('FILES', ROOT . 'files/');
define('DATA', SYS . 'data/');
define('LIB', SYS . 'library/');
define('ENG', SYS . 'engine/');
define('SEC', SYS . 'sections/');
define('CRON', LIB . 'cron/');

$start_point = $_SERVER['REQUEST_TIME'];

$mcache = new Memcache();
$mcache->connect('127.0.0.1', 11211) or exit('Ошибка подключения Memcache.' . PHP_EOL);

// Настройки
include(DATA . 'config.php');

// Проверка ключа и указания параметра
if ($argv[1] != $cfg['cron_key']) {
    exit('Invalid cron key' . PHP_EOL);
}
$task = $argv[2];

include(DATA . 'engine.php');
include(DATA . 'mysql.php');
include(DATA . 'params.php');

// Библиотеки
include(LIB . 'sql.php');
include(LIB . 'html.php');
include(LIB . 'system.php');
include(LIB . 'cron.php');
