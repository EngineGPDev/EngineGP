<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP')) {
    header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404');
    exit();
}

// Подключаем разделы
if (isset($url['id'])) {
    require_once SEC . 'monitoring/server.php';
} else {
    require_once SEC . 'monitoring/all.php';
}