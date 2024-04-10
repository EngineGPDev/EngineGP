<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Подключение filp/whoops
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
// логи в консоль браузера
$loggingInConsole = new \Whoops\Handler\PlainTextHandler();
$loggingInConsole->loggerOnly(true);
$loggingInConsole->setLogger((new \Monolog\Logger('EngineGP', [(new \Monolog\Handler\BrowserConsoleHandler())->setFormatter((new \Monolog\Formatter\LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInConsole);
// логи в файл
$loggingInFile = new \Whoops\Handler\PlainTextHandler();
$loggingInFile->loggerOnly(true);
$loggingInFile->setLogger((new \Monolog\Logger('EngineGP', [(new \Monolog\Handler\StreamHandler(ROOT . '/logs/enginegp.log'))->setFormatter((new \Monolog\Formatter\LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInFile);

// Парсинг адреса
$url = is_array(sys::url()) ? sys::url() : array();
$route = sys::url(false);
$section = isset($url['section']) ? $url['section'] : false;

$id = array_key_exists('id', $url) ? sys::int($url['id']) : false;
$go = array_key_exists('go', $url);
$page = array_key_exists('page', $url) ? sys::int($url['page']) : 1;
$route = $route == '' ? 'index' : $route;

session_start();

// Реферал
if (isset($_GET['account']))
    $_SESSION['referrer'] = sys::int($_GET['account']);

$auth = false;

// Проверка сессии на авторизацию
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $sql->query('SELECT `id`, `login`, `balance`, `group`, `level`, `time` FROM `users` WHERE `id`="' . $userId . '" LIMIT 1');
    if ($sql->num()) {
        $user = $sql->get();

        // Обновление активности
        if ($user['time'] + 10 < $start_point)
            $sql->query('UPDATE `users` set `time`="' . $start_point . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

        $auth = true;
    }
}

// Заголовок
$title = '';

// Навигация
$html->nav($cfg['name'], $cfg['http']);

include(DATA . 'header.php');

// Подключение файла
if (in_array($route, $aRoute))
    include(ENG . $route . '.php');
else
    include(ENG . '404.php');

// Обновление ссылок
if (isset($html->arr['main'])) {
    $html->upd(
        array(
            '[home]',
            '[js]',
            '[css]',
            '[img]'
        ),

        array(
            $cfg['http'],
            $cfg['http'] . 'template/js/',
            $cfg['http'] . 'template/css/',
            $cfg['http'] . 'template/images/'
        ),
        'main'
    );
}

// Онлайн игроков (общее количество всех игроков)
$aop='';
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
$html->set('description', sys::head('description'));
$html->set('keywords', sys::head('keywords'));
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
    $html->set('other_menu', isset($html->arr['vmenu']) ? $html->arr['vmenu'] : '');
} else {
    // Если пользователь не авторизован, выполните необходимые действия
    $html->set('other_menu', '');
}

$html->set('nav', isset($html->arr['nav']) ? $html->arr['nav'] : '', true);
$html->set('main', isset($html->arr['main']) ? $html->arr['main'] : '', true);

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

    if ($sql->num())
        $html->unitall('all', 'servers', 1, 1);
    else
        $html->unitall('all', 'servers', 0, 1);

    // Проверка наличия игрового сервера
    $servers = $sql->query('SELECT `id` FROM `control` WHERE `user`="' . $user['id'] . '" LIMIT 1');

    if ($sql->num())
        $html->unitall('all', 'control', 1);
    else
        $html->unitall('all', 'control', 0);

    $html->unitall('all', 'auth', 1, 1);
    $html->unitall('all', 'admin', $user['group'] == 'admin', 1);
    $html->unitall('all', 'support', $user['group'] == 'support', 1);
} else {
    $html->unitall('all', 'auth', 0, 1);
    $html->unitall('all', 'servers', 0, 1);
    $html->unitall('all', 'control', 0, 1);
    $html->unitall('all', 'admin', 0, 1);
    $html->unitall('all', 'support', 0, 1);
}
