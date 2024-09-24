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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$key = $url['key'] ?? sys::outjs(['e' => 'ключ не указан']);
$action = $url['action'] ?? sys::outjs(['e' => 'метод не указан']);

if (sys::valid($key, 'md5')) {
    sys::outjs(['e' => 'ключ имеет неправильный формат']);
}

$sql->query('SELECT `id`, `server` FROM `api` WHERE `key`="' . $key . '" LIMIT 1');
if (!$sql->num()) {
    sys::outjs(['e' => 'ключ не найден']);
}

$api = $sql->get();

$id = $api['server'];

include(LIB . 'games/games.php');
include(LIB . 'api.php');

if (in_array($action, ['start', 'restart', 'stop', 'change', 'reinstall', 'update'])) {
    $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['e' => 'сервер не найден']);
    }

    include(SEC . 'servers/action.php');
}

switch ($action) {
    case 'data':
        sys::outjs(api::data($id));

        // no break
    case 'load':
        sys::outjs(api::load($id));

        // no break
    case 'console':
        $cmd = $url['command'] ?? false;
        sys::outjs(api::console($id, $cmd));
}

sys::outjs(['e' => 'Метод не найден']);
