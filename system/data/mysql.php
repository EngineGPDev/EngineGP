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

use Symfony\Component\Dotenv\Dotenv;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Загружаем .env
$dotenv = new Dotenv();
$dotenv->load(ROOT.'.env');

define('USER_DATABASE', $_ENV['DB_USERNAME']);
define('PASSWORD_DATABASE', $_ENV['DB_PASSWORD']);
define('NAME_DATABASE', $_ENV['DB_DATABASE']);
define('CONNECT_DATABASE', $_ENV['DB_HOST']);
define('ERROR_DATABASE', false);
