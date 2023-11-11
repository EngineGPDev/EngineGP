<?php
	header('Content-Type: text/html; charset=utf-8');

	date_default_timezone_set('Europe/Moscow');

	@ini_set('display_errors', FALSE);
	@ini_set('html_errors', FALSE);
	@ini_set('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT);

	DEFINE('EGP', TRUE);
	DEFINE('ROOT', '../');
	DEFINE('SYS', ROOT.'system/');
	DEFINE('ACP', ROOT.'system/acp/');
	DEFINE('TPL', ROOT.'template/acp/');
	DEFINE('TEMP', ROOT.'temp/');
	DEFINE('FILES', ROOT.'files/');
	DEFINE('DATA', SYS.'data/');
	DEFINE('LIB', SYS.'library/');
	DEFINE('ENG', SYS.'acp/engine/');
	DEFINE('SEC', SYS.'acp/sections/');

	$start_point = $_SERVER['REQUEST_TIME'];

	$mcache = new Memcache;
	$mcache->connect('127.0.0.1', 11211) or exit('Ошибка: #mc0, обновите страницу позже, если ошибка повторяется, обратитесь в тех.поддержку: <a href="https://enginegp.ru">EGPv3</a>');

	// Настройки
	include(DATA.'config.php');
	include(DATA.'mysql.php');
	include(DATA.'params.php');
	include(DATA.'acpengine.php');

	// Библиотеки
	include(LIB.'sql.php');
	include(LIB.'html.php');
	include(LIB.'acpsystem.php');

	$uip = sys::ip();

	// Распределитель
	include(ACP.'distributor.php');

	// Выхлоп
	echo $html->arr['all'];
?>