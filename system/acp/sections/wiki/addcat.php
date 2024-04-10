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

if ($go) {
    $aData = array();

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

    if (in_array('', $aData))
        sys::outjs(array('e' => 'Необходимо заполнить все поля'));

    $sql->query('INSERT INTO `wiki_category` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`sort`="' . $aData['sort'] . '"');

    sys::outjs(array('s' => 'ok'));
}

$html->get('addcat', 'sections/wiki');

$html->pack('main');
