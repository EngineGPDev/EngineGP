<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Подключение filp/whoops
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
// Логи в консоль браузера
$loggingInConsole = new \Whoops\Handler\PlainTextHandler();
$loggingInConsole->loggerOnly(true);
$loggingInConsole->setLogger((new \Monolog\Logger('EngineGP', [(new \Monolog\Handler\BrowserConsoleHandler())->setFormatter((new \Monolog\Formatter\LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInConsole);
// Логи в файл
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

        // Проверка принадлежности к группе admin
        if ($user['group'] === "admin")
            $auth = true;
    }
}

if (!$auth)
    exit(header('Refresh: 0; URL=http://' . $cfg['url'] . '/403'));

// Подключение файла
if (in_array($route, $aRoute))
    include(ENG . $route . '.php');
else {
    $route = 'index';
    include(ENG . 'index.php');
}

// Обновление ссылок
if (isset($html->arr['main'])) {
    $html->upd(
        array(
            '[cur]',
            '[acp]',
            '[home]',
            '[js]',
            '[css]',
            '[img]'
        ),

        array(
            $cfg['currency'],
            $cfg['http'] . 'acp/',
            $cfg['http'],
            $cfg['http'] . 'template/acp/js/',
            $cfg['http'] . 'template/acp/css/',
            $cfg['http'] . 'template/acp/images/'
        ),
        'main'
    );
}

if (isset($html->arr['menu'])) {
    $html->upd(
        array(
            '[acp]',
            '[home]',
            '[js]',
            '[css]',
            '[img]'
        ),

        array(
            $cfg['http'] . 'acp/',
            $cfg['http'],
            $cfg['http'] . 'template/acp/js/',
            $cfg['http'] . 'template/acp/css/',
            $cfg['http'] . 'template/acp/images/'
        ),
        'menu'
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
} else
    $html->unit('section');

$html->unit('p_' . $route, true);

unset($aRoute[array_search($route, $aRoute)]);

foreach ($aRoute as $route)
    $html->unit('p_' . $route);

$html->set('main', isset($html->arr['main']) ? $html->arr['main'] : '', true);

$html->pack('all');
