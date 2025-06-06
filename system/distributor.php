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
// логи в консоль браузера
$loggingInConsole = new PlainTextHandler();
$loggingInConsole->loggerOnly(true);
$loggingInConsole->setLogger((new Logger('EngineGP', [(new BrowserConsoleHandler())->setFormatter((new LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInConsole);
// логи в файл
$loggingInFile = new PlainTextHandler();
$loggingInFile->loggerOnly(true);
$loggingInFile->setLogger((new Logger('EngineGP', [(new StreamHandler(ROOT . '/logs/enginegp.log'))->setFormatter((new LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInFile);
$whoops->register();

// Парсинг адреса
$url = is_array(System::url()) ? System::url() : [];
$route = System::url(false);
$section = $url['section'] ?? false;

$id = array_key_exists('id', $url) ? System::int($url['id']) : false;
$go = array_key_exists('go', $url);
$page = array_key_exists('page', $url) ? System::int($url['page']) : 1;
$route = $route == '' ? 'index' : $route;

// Реферал
if (isset($_GET['account'])) {
    setcookie('referrer', System::int($_GET['account']), $start_point + (10 * 86400), "/", $_SERVER['HTTP_HOST'], false, true);
}

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

            // Генерация JWT токена
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

            $auth = true;
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

// Заголовок
$title = '';

// Навигация
$html->nav($cfg['name'], $cfg['http']);

include(DATA . 'header.php');

// Подключение файла
if (in_array($route, $aRoute)) {
    include(ENG . $route . '.php');
} else {
    include(ENG . '404.php');
}

// Обновление ссылок
if (isset($html->arr['main'])) {
    $html->upd(
        'main',
        [
            '[home]',
            '[js]',
            '[css]',
            '[img]',
        ],
        [
            $cfg['http'],
            $cfg['http'] . 'template/js/',
            $cfg['http'] . 'template/css/',
            $cfg['http'] . 'template/images/',
        ]
    );
}

// Онлайн игроков (общее количество всех игроков)
$aop = '';
//$aop = $mcache->get('all_online_players'); //Если ваш хостинг чувствует себя плохо из за чрезмерной нагрузки от данного модуля, то включите кеширование, раскомментировав этот кусочек кода
if ($aop == '') {
    $sql->query('SELECT SUM(`online`) FROM `servers` WHERE `status`="working" OR `status`="change"');
    $sum = $sql->get();

    $aop = $sum['SUM(`online`)'];

    $mcache->set('all_online_players', $aop, false, 1);
}

// Заготовка выхлопа
$html->get('all');
$html->set('title', $title . ' | ' . $cfg['name']);
$html->set('description', System::head('description'));
$html->set('keywords', System::head('keywords'));
$html->set('home', $cfg['http']);
$html->set('js', $cfg['http'] . 'template/js/');
$html->set('css', $cfg['http'] . 'template/css/');
$html->set('img', $cfg['http'] . 'template/images/');
$html->set('aop', $aop);
$html->set('cur', $cfg['currency']);

// Если авторизован
if ($auth) {
    // Здесь вы можете использовать информацию о пользователе, например, $user['balance']
    $html->set('login', $user['login']);
    $html->set('balance', round($user['balance'], 2));
    $html->set('other_menu', $html->arr['vmenu'] ?? '');
} else {
    // Если пользователь не авторизован, выполните необходимые действия
    $html->set('other_menu', '');
}

$html->set('nav', $html->arr['nav'] ?? '', true);
$html->set('main', $html->arr['main'] ?? '', true);

$sql->query('SELECT `id`, `login`, `time` FROM `users` ORDER BY `id` ASC');
$online = '<span style="padding:0 5px;">';
while ($staff = $sql->get()) {
    if ($staff['time'] + 15 > $start_point) {
        $online .= $staff['login'] . ', ';
    } else {
        $online .= '';
    }
}
$online .= '</span>';
$html->set('online_users', $online);
$html->pack('all');

// Блоки
if ($auth) {
    // Проверка наличия игрового сервера
    $servers = $sql->query('(SELECT `id` FROM `servers` WHERE `user`="' . $user['id'] . '" LIMIT 1) UNION (SELECT `id` FROM `owners` WHERE `user`="' . $user['id'] . '" LIMIT 1)');

    if ($sql->num()) {
        $html->unitall('servers', 'all', 1, 1);
    } else {
        $html->unitall('servers', 'all', 0, 1);
    }

    $html->unitall('auth', 'all', 1, 1);
    $html->unitall('admin', 'all', $user['group'] == 'admin', 1);
    $html->unitall('support', 'all', $user['group'] == 'support', 1);
} else {
    $html->unitall('auth', 'all', 0, 1);
    $html->unitall('servers', 'all', 0, 1);
    $html->unitall('admin', 'all', 0, 1);
    $html->unitall('support', 'all', 0, 1);
}
