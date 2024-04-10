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

if ($user['group'] != 'admin')
    sys::outjs(array('e' => 'У вас нет доступа к данному действию.'));

if ($id) {
    $sql->query('DELETE FROM `help` WHERE `id`="' . $id . '" LIMIT 1');

    $dialogs = $sql->query('SELECT `id`, `img` FROM `help_dialogs` WHERE `help`="' . $id . '"');
    while ($dialog = $sql->get($dialogs)) {
        $aImg = sys::b64djs($dialog['img']);

        foreach ($aImg as $img) {
            $sql->query('DELETE FROM `help_upload` WHERE `name`="' . $img . '" LIMIT 1');

            unlink(ROOT . 'upload/' . $img);
        }

        $sql->query('DELETE FROM `help_dialogs` WHERE `id`="' . $dialog['id'] . '" LIMIT 1');
    }

    sys::outjs(array('s' => 'ok'));
}

sys::outjs(array('e' => 'Вопрос не найден в базе.'));
