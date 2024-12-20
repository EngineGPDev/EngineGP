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

if (isset($url['delete']) and $url['delete'] == 'all') {
    $sql->query('DELETE FROM `auth` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `logs` WHERE `user`="' . $id . '"');

    $helps = $sql->query('SELECT `id` FROM `help` WHERE `user`="' . $id . '"');
    while ($help = $sql->get($helps)) {
        $sql->query('DELETE FROM `help_dialogs` WHERE `help`="' . $help['id'] . '"');
        $sql->query('DELETE FROM `help` WHERE `id`="' . $help['id'] . '" LIMIT 1');
    }

    $uploads = $sql->query('SELECT `id`, `name` FROM `help_upload` WHERE `user`="' . $id . '"');
    while ($upload = $sql->get($uploads)) {
        @unlink(ROOT . 'upload/' . $upload['name']);

        $sql->query('DELETE FROM `help_upload` WHERE `id`="' . $upload['id'] . '" LIMIT 1');
    }

    $sql->query('DELETE FROM `logs_sys` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `owners` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `promo_use` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `recovery` WHERE `user`="' . $id . '"');
    $sql->query('DELETE FROM `security` WHERE `user`="' . $id . '"');

    $sql->query('UPDATE `servers` set `user`="0" WHERE `user`="' . $id . '"');
    $sql->query('UPDATE `web` set `user`="0" WHERE `user`="' . $id . '"');
}

$sql->query('DELETE FROM `users` WHERE `id`="' . $id . '" LIMIT 1');

sys::outjs(['s' => 'ok']);
