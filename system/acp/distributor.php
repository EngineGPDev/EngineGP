<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$device = '!mobile';
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
$loggingInFile->setLogger((new \Monolog\Logger('EngineGP', [(new \Monolog\Handler\StreamHandler(DIR . 'logs/enginegp.log'))->setFormatter((new \Monolog\Formatter\LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInFile);

// Парсинг адреса
$url = is_array(sys::url()) ? sys::url() : array();
$route = sys::url(false);
$section = isset($url['section']) ? $url['section'] : false;

$id = array_key_exists('id', $url) ? sys::int($url['id']) : false;
$go = array_key_exists('go', $url);
$page = array_key_exists('page', $url) ? sys::int($url['page']) : 1;
$route = $route == '' ? 'index' : $route;

$auth = false;

// Проверка cookie на авторизацию
$aAuth = array();

$aAuth['login'] = isset($_COOKIE['egp_login']) ? $_COOKIE['egp_login'] : '';
$aAuth['passwd'] = isset($_COOKIE['egp_passwd']) ? $_COOKIE['egp_passwd'] : '';
$aAuth['authkeycheck'] = isset($_COOKIE['egp_authkeycheck']) ? $_COOKIE['egp_authkeycheck'] : '';

$authkey = md5($aAuth['login'] . $uip . $aAuth['passwd']);

if (!in_array('', $aAuth) and $authkey == $aAuth['authkeycheck']) {
    if ((!sys::valid($aAuth['login'], 'other', $aValid['login'])) and !sys::valid($aAuth['passwd'], 'md5')) {
        $sql->query('SELECT `id` FROM `users` WHERE `login`="' . $aAuth['login'] . '" AND `passwd`="' . $aAuth['passwd'] . '" AND `group`="admin" LIMIT 1');
        if ($sql->num()) {
            $sql->query('SELECT `id`, `login`, `balance`, `group`, `time` FROM `users` WHERE `login`="' . $aAuth['login'] . '" AND `passwd`="' . $aAuth['passwd'] . '" LIMIT 1');
            $user = $sql->get();

            // Обновление активности
            if ($user['time'] + 10 < $start_point)
                $sql->query('UPDATE `users` set `time`="' . $start_point . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

            $auth = true;
        }
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
        'main',

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
            $cfg['http'] . 'acp/template/js/',
            $cfg['http'] . 'acp/template/css/',
            $cfg['http'] . 'acp/template/images/'
        ),
    );
}

if (isset($html->arr['menu'])) {
    $html->upd(
        'menu',

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
            $cfg['http'] . 'acp/template/js/',
            $cfg['http'] . 'acp/template/css/',
            $cfg['http'] . 'acp/template/images/'
        ),
    );
}

// Заготовка выхлопа
$html->get('all');

$html->set('acp', $cfg['http'] . 'acp/');
$html->set('admin', $user['id']);
$html->set('home', $cfg['http']);
$html->set('js', $cfg['http'] . 'acp/template/js/');
$html->set('css', $cfg['http'] . 'acp/template/css/');
$html->set('img', $cfg['http'] . 'acp/template/images/');

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
?>