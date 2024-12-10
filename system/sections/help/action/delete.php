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
    $sql->query('DELETE FROM `help` WHERE `id`="' . $id . '" LIMIT 1');

    $dialogs = $sql->query('SELECT `id`, `img` FROM `help_dialogs` WHERE `help`="' . $id . '"');
    while ($dialog = $sql->get($dialogs)) {
        $aImg = sys::b64djs($dialog['img']);

        foreach ($aImg as $img) {
            $sql->query('DELETE FROM `help_upload` WHERE `name`="' . $img . '" LIMIT 1');

            $filePath = ROOT . 'upload/' . $img;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $sql->query('DELETE FROM `help_dialogs` WHERE `id`="' . $dialog['id'] . '" LIMIT 1');
    }

    sys::outjs(['s' => 'ok']);
}

sys::outjs(['e' => 'Вопрос не найден в базе.']);
