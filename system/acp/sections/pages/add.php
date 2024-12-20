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
    $aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : '';

    if (in_array('', $aData)) {
        sys::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    $name = md5(time() . rand(5, 100) . rand(10, 20) . rand(1, 20) . rand(40, 80));

    $file = fopen(FILES . 'pages/' . $name, "w");

    fputs($file, $aData['text']);

    fclose($file);

    $sql->query('INSERT INTO `pages` set `name`="' . htmlspecialchars($aData['name']) . '", `file`="' . $name . '"');

    sys::outjs(['s' => 'ok']);
}

$html->get('add', 'sections/pages');

$html->pack('main');
