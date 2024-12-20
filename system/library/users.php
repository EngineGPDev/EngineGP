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

class users
{
    public static function ava($user)
    {
        global $cfg;

        $file = 'upload/avatars/' . $user . '.';
        $link = $cfg['http'] . 'upload/avatars/' . $user . '.';

        if (file_exists(ROOT . $file . 'jpg')) {
            return $link . 'jpg';
        }

        if (file_exists(ROOT . $file . 'png')) {
            return $link . 'png';
        }

        if (file_exists(ROOT . $file . 'gif')) {
            return $link . 'gif';
        }

        return $cfg['http'] . 'template/images/avatar.png';
    }

    public static function nav($active)
    {
        global $cfg, $html;

        $aUnit = ['index', 'settings', 'auth', 'logs', 'security'];

        $html->get('gmenu', 'sections/user');

        $html->set('home', $cfg['http']);

        foreach ($aUnit as $unit) {
            if ($unit == $active) {
                $html->unit($unit, 1);
            } else {
                $html->unit($unit);
            }
        }

        $html->pack('main');

        $html->get('vmenu', 'sections/user');

        $html->set('home', $cfg['http']);

        foreach ($aUnit as $unit) {
            if ($unit == $active) {
                $html->unit($unit, 1);
            } else {
                $html->unit($unit);
            }
        }

        $html->pack('vmenu');

        return null;
    }
}
