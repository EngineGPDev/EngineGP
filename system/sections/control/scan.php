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

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

// Запрошена информация (cpu, ram, hdd)
if (isset($url['resources']))
    sys::outjs(ctrl::resources($id));

// Запрошена подробная информация (cpu, ram, hdd)
if (isset($url['update_info']))
    sys::outjs(ctrl::update_info($id));

// Обновление информации (status, time)
if (isset($url['update_status']))
    sys::outjs(ctrl::update_status($id));

exit;
