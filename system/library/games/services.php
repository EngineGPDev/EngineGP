<?php

/*
 * Copyright 2018-2025 Solovev Sergei
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

class services
{
    public static function unit($game)
    {
        global $sql;

        $sel = 0;

        $units = $sql->query('SELECT `id` FROM `units` WHERE `' . $game . '`="1" AND `show`="1" ORDER BY `sort` ASC');
        while ($unit = $sql->get($units)) {
            $sql->query('SELECT `id` FROM `tarifs` WHERE `unit`="' . $unit['id'] . '" AND `game`="' . $game . '" AND `show`="1" LIMIT 1');
            if (!$sql->num()) {
                continue;
            } else {
                $sel = $unit['id'];
                break;
            }
        }

        return 'SELECT `id`, `test` FROM `units` WHERE `id`="' . $sel . '" LIMIT 1';
    }

    public static function units($game)
    {
        global $sql;

        $list = '';

        $units = $sql->query('SELECT `id`, `name` FROM `units` WHERE `' . $game . '`="1" AND `show`="1" ORDER BY `sort` ASC');
        while ($unit = $sql->get($units)) {
            $sql->query('SELECT `id` FROM `tarifs` WHERE `unit`="' . $unit['id'] . '" AND `game`="' . $game . '" AND `show`="1" LIMIT 1');
            if ($sql->num()) {
                $list .= '<option value="' . $unit['id'] . '">#' . $unit['id'] . ' ' . $unit['name'] . '</option>';
            }
        }

        return $list;
    }

    public static function tarifs($game, $unit)
    {
        global $sql;

        $list = '';

        $sql->query('SELECT `id`, `name` FROM `tarifs` WHERE `game`="' . $game . '" AND `unit`="' . $unit . '" AND `show`="1" ORDER BY `sort` ASC');
        while ($tarif = $sql->get()) {
            $list .= '<option value="' . $tarif['id'] . '">' . $tarif['name'] . '</option>';
        }

        return $list;
    }
}
