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

if (!isset($url['response']) and !in_array($url['response'], ['success', 'fail'])) {
    exit();
}

if ($url['response'] == 'success') {
    sys::out(file_get_contents(ROOT . 'success.html'));
} else {
    sys::out(file_get_contents(ROOT . 'fail.html'));
}
