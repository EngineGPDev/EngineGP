<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

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
