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

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $aData['sort'] = isset($_POST['sort']) ? sys::int($_POST['sort']) : 0;

    if (in_array('', $aData)) {
        sys::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    $sql->query('INSERT INTO `wiki_category` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`sort`="' . $aData['sort'] . '"');

    sys::outjs(['s' => 'ok']);
}

$html->get('addcat', 'sections/wiki');

$html->pack('main');
