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

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\PlainTextHandler;
use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Подключение filp/whoops
$whoops = new Run();
$prettyPageHandler = new PrettyPageHandler();
foreach ($cfg['whoops']['blacklist'] as $key => $secrets) {
    foreach ($secrets as $secret) {
        $prettyPageHandler->blacklist($key, $secret);
    }
}
$whoops->pushHandler($prettyPageHandler);
// Логи в консоль браузера
$loggingInConsole = new PlainTextHandler();
$loggingInConsole->loggerOnly(true);
$loggingInConsole->setLogger((new Logger('EngineGP', [(new BrowserConsoleHandler())->setFormatter((new LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInConsole);
// Логи в файл
$loggingInFile = new PlainTextHandler();
$loggingInFile->loggerOnly(true);
$loggingInFile->setLogger((new Logger('EngineGP', [(new StreamHandler(ROOT . '/logs/enginegp.log'))->setFormatter((new LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInFile);
$whoops->register();

// Парсинг адреса
$url = is_array(sys::url()) ? sys::url() : [];
$route = sys::url(false);
$section = $url['section'] ?? false;

$id = array_key_exists('id', $url) ? sys::int($url['id']) : false;
$go = array_key_exists('go', $url);
$page = array_key_exists('page', $url) ? sys::int($url['page']) : 1;
$route = $route == '' ? 'index' : $route;

$auth = false;
$user = [];

// Получение токена из куки
$refreshToken = $_COOKIE['refresh_token'] ?? null;

// Порог обновления токена
$refreshThreshold = 86400 * 7;

if ($refreshToken) {
    try {
        // Проверка токена
        $decodedJwt = JWT::decode($refreshToken, new Key($_ENV['JWT_KEY'], 'HS256'));

        // Токен валиден
        $user['id'] = $decodedJwt->id;

        // Если токен истекает менее чем через 7 дней, создаём новый
        if ($decodedJwt->exp - $start_point < $refreshThreshold) {
            $payload = [
                'id' => $user['id'],
                'iat' => $start_point,
                'exp' => $start_point + 86400 * 30,
            ];

            // Генерация нового токена
            $refreshToken = JWT::encode($payload, $_ENV['JWT_KEY'], 'HS256');

            // Обновление куки с новым токеном
            setcookie('refresh_token', $refreshToken, [
                'expires' => $start_point + 86400 * 30,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'samesite' => 'Strict',
            ]);
        }

        // Получение информации о пользователе из базы данных
        $sql->query('SELECT `id`, `login`, `balance`, `group`, `level`, `time` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
        if ($sql->num()) {
            $user = $sql->get();

            // Обновление активности
            if ($user['time'] + 10 < $start_point) {
                $sql->query('UPDATE `users` set `time`="' . $start_point . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');
            }

            // Проверка принадлежности к группе admin
            if ($user['group'] === "admin") {
                $auth = true;
            }
        }
    } catch (Exception $e) {
        // Если токен недействителен, удаляем куку
        setcookie('refresh_token', '', [
            'expires' => $start_point - 3600,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'samesite' => 'Strict',
        ]);
    }
} else {
    // Токен не передан
    $user['id'] = null;
}

if (!$auth) {
    exit(header('Refresh: 0; URL=http://' . $cfg['url'] . '/403'));
}

// Подключение файла
if (in_array($route, $aRoute)) {
    include(ENG . $route . '.php');
} else {
    $route = 'index';
    include(ENG . 'index.php');
}

// Обновление ссылок
if (isset($html->arr['main'])) {
    $html->upd(
        'main',
        [
            '[cur]',
            '[acp]',
            '[home]',
            '[js]',
            '[css]',
            '[img]',
        ],
        [
            $cfg['currency'],
            $cfg['http'] . 'acp/',
            $cfg['http'],
            $cfg['http'] . 'template/acp/js/',
            $cfg['http'] . 'template/acp/css/',
            $cfg['http'] . 'template/acp/images/',
        ]
    );
}

if (isset($html->arr['menu'])) {
    $html->upd(
        'menu',
        [
            '[acp]',
            '[home]',
            '[js]',
            '[css]',
            '[img]',
        ],
        [
            $cfg['http'] . 'acp/',
            $cfg['http'],
            $cfg['http'] . 'template/acp/js/',
            $cfg['http'] . 'template/acp/css/',
            $cfg['http'] . 'template/acp/images/',
        ]
    );
}

// Заготовка выхлопа
$html->get('all');

$html->set('acp', $cfg['http'] . 'acp/');
$html->set('admin', $user['id']);
$html->set('home', $cfg['http']);
$html->set('js', $cfg['http'] . 'template/acp/js/');
$html->set('css', $cfg['http'] . 'template/acp/css/');
$html->set('img', $cfg['http'] . 'template/acp/images/');

if (isset($html->arr['menu'])) {
    $html->unit('section', true);
    $html->set('info', $info);
    $html->set('menu', $html->arr['menu']);
} else {
    $html->unit('section');
}

$html->unit('p_' . $route, true);

unset($aRoute[array_search($route, $aRoute)]);

foreach ($aRoute as $route) {
    $html->unit('p_' . $route);
}

$html->set('main', $html->arr['main'] ?? '', true);

$html->pack('all');
