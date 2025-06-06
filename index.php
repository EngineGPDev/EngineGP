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
use EngineGP\View\Html;
use Symfony\Component\Dotenv\Dotenv;

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: EngineGP - Control panel');
date_default_timezone_set('Europe/Moscow');

// Composer
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Please <a href="https://getcomposer.org/download/" target="_blank" rel="noreferrer" style="color:#0a25bb;">install composer</a> and run <code style="background:#222;color:#00e01f;padding:2px 6px;border-radius:3px;">composer install</code>');
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
define('DIR', dirname('index.php'));
define('ROOT', DIR . '/');
define('SYS', ROOT . 'system/');
define('TPL', ROOT . 'template/');
define('TEMP', ROOT . 'temp/');
define('FILES', ROOT . 'files/');
define('DATA', SYS . 'data/');
define('LIB', SYS . 'library/');
define('ENG', SYS . 'engine/');
define('SEC', SYS . 'sections/');

// Declaring variables
$start_point = $_SERVER['REQUEST_TIME'];
$mcache = new Memcache();
$html = new Html();
$uip = System::ip();

// Connecting to memcache
$mcache->connect('127.0.0.1', 11211) or exit('Error connecting to Memcache server.');

// Настройки
include(DATA . 'config.php');
include(DATA . 'engine.php');
include(DATA . 'mysql.php');

// Библиотеки
include(LIB . 'sql.php');

// Распределитель
include(SYS . 'distributor.php');

// Выхлоп
echo $html->arr['all'];
