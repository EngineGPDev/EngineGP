<?php
header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: EGP');

date_default_timezone_set('Europe/Moscow');

@ini_set('display_errors', TRUE);
@ini_set('html_errors', TRUE);
@ini_set('error_reporting', E_ALL);

define('EGP', TRUE);
define('DIR', __DIR__ . '/');
define('SYS', DIR . 'system/');
define('TPL', DIR . 'template/');
define('TEMP', DIR . 'temp/');
define('FILES', DIR . 'files/');
define('DATA', SYS . 'data/');
define('LIB', SYS . 'library/');
define('ENG', SYS . 'engine/');
define('SEC', SYS . 'sections/');

$start_point = $_SERVER['REQUEST_TIME'];

if (!class_exists('Memcache')) {
    die('Please install Memcache extension');
}
$mcache = new Memcache;
$mcache->connect('127.0.0.1', 11211) or exit('Ошибка подключения Memcache');

// Composer
if (!file_exists(DIR . 'vendor/autoload.php')) {
    die('Please <a href="https://getcomposer.org/download/" target="_blank" rel="noreferrer" style="color:#0a25bb;">install composer</a> and run <code style="background:#222;color:#00e01f;padding:2px 6px;border-radius:3px;">composer install</code>');
}
require(DIR . 'vendor/autoload.php');

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
?>