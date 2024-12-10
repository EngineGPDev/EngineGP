<?php

/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: EngineGP - Control panel');
date_default_timezone_set('Europe/Moscow');

// Composer
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    die('Please <a href="https://getcomposer.org/download/" target="_blank" rel="noreferrer" style="color:#0a25bb;">install composer</a> and run <code style="background:#222;color:#00e01f;padding:2px 6px;border-radius:3px;">composer install</code>');
}
require(__DIR__ . '/vendor/autoload.php');

// Загружаем .env
$dotenv = new Symfony\Component\Dotenv\Dotenv();
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

$start_point = $_SERVER['REQUEST_TIME'];

$mcache = new Memcache();
$mcache->connect('127.0.0.1', 11211) or exit('Ошибка подключения Memcache');

// Настройки
include(DATA . 'config.php');
include(DATA . 'engine.php');
include(DATA . 'mysql.php');
include(DATA . 'params.php');

// Библиотеки
include(LIB . 'sql.php');
include(LIB . 'html.php');
include(LIB . 'system.php');

$uip = sys::ip();

// Распределитель
include(SYS . 'distributor.php');

// Выхлоп
echo $html->arr['all'];
