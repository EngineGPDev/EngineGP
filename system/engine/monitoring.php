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
    header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404');
    exit();
}

// Подключаем разделы
if (isset($url['id'])) {
    require_once SEC . 'monitoring/server.php';
} else {
    require_once SEC . 'monitoring/all.php';
}
