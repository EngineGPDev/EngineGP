<?php
date_default_timezone_set('Europe/Moscow');

@ini_set('display_errors', TRUE);
@ini_set('html_errors', TRUE);
@ini_set('error_reporting', E_ALL);

DEFINE('EGP', TRUE);
DEFINE('DIR', __DIR__);
DEFINE('ROOT', DIR.'/');
DEFINE('SYS', ROOT.'system/');
DEFINE('TPL', ROOT.'template/');
DEFINE('TEMP', ROOT.'temp/');
DEFINE('FILES', ROOT.'files/');
DEFINE('DATA', SYS.'data/');
DEFINE('LIB', SYS.'library/');
DEFINE('ENG', SYS.'engine/');
DEFINE('SEC', SYS.'sections/');
DEFINE('CRON', LIB.'cron/');

$start_point = $_SERVER['REQUEST_TIME'];

$mcache = new Memcache;
$mcache->connect('127.0.0.1', 11211) OR exit('Ошибка: не удалось создать связь с Memcache.'.PHP_EOL);

// Composer
if (!file_exists(ROOT.'vendor/autoload.php')) {
    die('Please <a href="https://getcomposer.org/download/" target="_blank" rel="noreferrer" style="color:#0a25bb;">install composer</a> and run <code style="background:#222;color:#00e01f;padding:2px 6px;border-radius:3px;">composer install</code>');
}
require(ROOT.'vendor/autoload.php');

// Настройки
include(DATA.'config.php');
include(DATA.'engine.php');
include(DATA.'mysql.php');
include(DATA.'params.php');

// Проверка ключа и указания параметра
if($argv[1] != $cfg['cron_key'])
    exit('Invalid cron key' . PHP_EOL);
$task = $argv[2];

// Библиотеки
include(LIB.'sql.php');
include(LIB.'html.php');
include(LIB.'system.php');
include(LIB.'cron.php');
?>