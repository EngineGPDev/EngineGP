<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Загружаем .env
$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(ROOT.'.env');

define('USER_DATABASE', $_ENV['DB_USERNAME']);
define('PASSWORD_DATABASE', $_ENV['DB_PASSWORD']);
define('NAME_DATABASE', $_ENV['DB_DATABASE']);
define('CONNECT_DATABASE', $_ENV['DB_HOST']);
define('ERROR_DATABASE', false);
