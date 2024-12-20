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

$html->nav('Раздел недоступен');

if ($server['time'] < $start_point) {
    $html->get('overdue');
} else {
    $status = [
        'install' => 'установки',
        'reinstall' => 'переустановки',
        'update' => 'обновления',
        'recovery' => 'восстановления',
        'blocked' => 'блокировки',
    ];

    $html->get('noaccess');

    $html->set('status', $status[$server['status']]);
}

$html->pack('main');
