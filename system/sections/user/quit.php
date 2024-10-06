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

// Проверка на авторизацию
sys::noauth($auth, $go);

setcookie('refresh_token', '', [
    'expires' => $start_point - 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'samesite' => 'Strict',
]);

// Обновление активности
$sql->query('UPDATE `users` set `time`="' . ($start_point - 10) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

sys::back($cfg['http']);
