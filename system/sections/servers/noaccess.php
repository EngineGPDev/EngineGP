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

$html->nav('Раздел недоступен');

if ($server['time'] < $start_point)
    $html->get('overdue');
else {
    $status = array(
        'install' => 'установки',
        'reinstall' => 'переустановки',
        'update' => 'обновления',
        'recovery' => 'восстановления',
        'blocked' => 'блокировки'
    );

    $html->get('noaccess');

    $html->set('status', $status[$server['status']]);
}

$html->pack('main');
