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

if (!$go)
    exit;

if ($user['group'] != 'admin')
    sys::outjs(array('i' => 'Чтобы удалить услугу, создайте вопрос выбрав свой сервер с причиной удаления.'), $nmch);

include(LIB . 'web/free.php');

$aData = array(
    'type' => $url['subsection'],
    'server' => array_merge($server, array('id' => $id))
);

web::delete($aData, $nmch);
