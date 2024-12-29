<?php

/*
 * Copyright 2018-2024 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
