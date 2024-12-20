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
    $aData['text'] = isset($_POST['text']) ? sys::bbc(trim($_POST['text'])) : '';
    $aData['full'] = isset($_POST['full']) ? sys::bbc(trim($_POST['full'])) : '';
    $aData['tags'] = isset($_POST['tags']) ? trim($_POST['tags']) : '';

    if (in_array('', $aData)) {
        sys::outjs(['e' => 'Необходимо заполнить все поля']);
    }

    if (sys::strlen($aData['name']) > 50) {
        sys::outjs(['e' => 'Заголовок не должен превышать 50 символов.']);
    }

    if (sys::strlen($aData['tags']) > 100) {
        sys::outjs(['e' => 'Теги не должен превышать 100 символов.']);
    }

    $sql->query('INSERT INTO `news` set '
        . '`name`="' . htmlspecialchars($aData['name']) . '",'
        . '`text`="' . htmlspecialchars($aData['text']) . '",'
        . '`full_text`="' . htmlspecialchars($aData['full']) . '",'
        . '`tags`="' . htmlspecialchars($aData['tags']) . '",'
        . '`views`="0",'
        . '`date`="' . $start_point . '"');

    sys::outjs(['s' => 'ok']);
}

$html->get('add', 'sections/news');

$html->pack('main');
