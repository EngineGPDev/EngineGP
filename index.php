<?php
	header('Content-Type: text/html; charset=utf-8');
	header('X-Powered-By: EGP');

	date_default_timezone_set('Europe/Moscow');

	@ini_set('display_errors', TRUE);
	@ini_set('html_errors', TRUE);
	@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT);

	DEFINE('EGP', TRUE);
	DEFINE('DIR', dirname('index.php'));
	DEFINE('ROOT', DIR.'/');
	DEFINE('SYS', ROOT.'system/');
	DEFINE('TPL', ROOT.'template/');
	DEFINE('TEMP', ROOT.'temp/');
	DEFINE('FILES', ROOT.'files/');
	DEFINE('DATA', SYS.'data/');
	DEFINE('LIB', SYS.'library/');
	DEFINE('ENG', SYS.'engine/');
	DEFINE('SEC', SYS.'sections/');

	$start_point = $_SERVER['REQUEST_TIME'];

	$mcache = new Memcache;
	$mcache->connect('127.0.0.1', 11211) or exit('Ошибка подключения Memcache');

	// Настройки
	include(DATA.'config.php');
	include(DATA.'engine.php');
	include(DATA.'mysql.php');
	include(DATA.'params.php');

	// Библиотеки
	include(LIB.'sql.php');
	include(LIB.'html.php');
	include(LIB.'system.php');

	$uip = sys::ip();

	// Распределитель
	include(SYS.'distributor.php');

	// Выхлоп
	echo $html->arr['all'];
?>