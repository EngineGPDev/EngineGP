<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$key = $url['key'] ?? sys::outjs(['e' => 'ключ не указан']);
$action = $url['action'] ?? sys::outjs(['e' => 'метод не указан']);

if (sys::valid($key, 'md5'))
    sys::outjs(['e' => 'ключ имеет неправильный формат']);

$sql->query('SELECT `id`, `server` FROM `api` WHERE `key`="' . $key . '" LIMIT 1');
if (!$sql->num())
    sys::outjs(['e' => 'ключ не найден']);

$api = $sql->get();

$id = $api['server'];

require(LIB . 'games/games.php');
require(LIB . 'api.php');

if (in_array($action, ['start', 'restart', 'stop', 'change', 'reinstall', 'update'])) {
    $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
    if (!$sql->num())
        sys::outjs(['e' => 'сервер не найден']);

    require(SEC . 'servers/action.php');
}

switch ($action) {
    case 'data':
        sys::outjs((new api())->data($id));

    case 'load':
        sys::outjs((new api())->load($id));

    case 'console':
        $cmd = $url['command'] ?? false;

        sys::outjs((new api())->console($id, $cmd));
}

sys::outjs(['e' => 'Метод не найден']);
