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

if ($user['group'] != 'admin') {
    sys::outjs(['e' => 'У вас нет доступа к данному действию.']);
}

if ($id) {
    $msg = isset($url['msg']) ? sys::int($url['msg']) : sys::outjs(['s' => 'ok']);

    $sql->query('SELECT `img` FROM `help_dialogs` WHERE `id`="' . $msg . '" LIMIT 1');
    if (!$sql->num()) {
        sys::outjs(['s' => 'ok']);
    }

    $images = $sql->get();

    $aImg = sys::b64djs($images['img']);

    foreach ($aImg as $img) {
        $sql->query('DELETE FROM `help_upload` WHERE `name`="' . $img . '" LIMIT 1');

        unlink(ROOT . 'upload/' . $img);
    }

    $sql->query('DELETE FROM `help_dialogs` WHERE `id`="' . $msg . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
}

sys::outjs(['e' => 'Вопрос не найден в базе.']);
