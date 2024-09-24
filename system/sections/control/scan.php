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

// Запрошена информация (cpu, ram, hdd)
if (isset($url['resources'])) {
    sys::outjs(ctrl::resources($id));
}

// Запрошена подробная информация (cpu, ram, hdd)
if (isset($url['update_info'])) {
    sys::outjs(ctrl::update_info($id));
}

// Обновление информации (status, time)
if (isset($url['update_status'])) {
    sys::outjs(ctrl::update_status($id));
}

exit;
