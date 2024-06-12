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

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$key = isset($url['key']) ? $url['key'] : sys::outjs(array('e' => 'ключ не указан'));
$action = isset($url['action']) ? $url['action'] : sys::outjs(array('e' => 'метод не указан'));

if (sys::valid($key, 'md5'))
    sys::outjs(array('e' => 'ключ имеет неправильный формат'));

$sql->query('SELECT `id`, `server` FROM `api` WHERE `key`="' . $key . '" LIMIT 1');
if (!$sql->num())
    sys::outjs(array('e' => 'ключ не найден'));

$api = $sql->get();

$id = $api['server'];

include(LIB . 'games/games.php');
include(LIB . 'api.php');

if (in_array($action, array('start', 'restart', 'stop', 'change', 'reinstall', 'update'))) {
    $sql->query('SELECT `id` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
    if (!$sql->num())
        sys::outjs(array('e' => 'сервер не найден'));

    include(SEC . 'servers/action.php');
}

switch ($action) {
    case 'data':
        sys::outjs(api::data($id));

    case 'load':
        sys::outjs(api::load($id));

    case 'console':
        $cmd = isset($url['command']) ? $url['command'] : false;

        sys::outjs(api::console($id, $cmd));
}

sys::outjs(array('e' => 'Метод не найден'));
