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

use EngineGP\System;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class privileges extends cron
{
    public function __construct()
    {
        global $sql, $argv, $start_point;

        $servers = $argv;

        unset($servers[0], $servers[1], $servers[2]);

        $game = $servers[3];

        if (!array_key_exists($game, cron::$admins_file)) {
            return null;
        }

        $sql->query('SELECT `address` FROM `units` WHERE `id`="' . $servers[4] . '" LIMIT 1');
        if (!$sql->num()) {
            return 'Ошибка: UNIT#' . $servers[4] . ' не найден.';
        }

        $unit = $sql->get();

        unset($servers[3], $servers[4]);

        foreach ($servers as $i => $id) {
            $sql->query('SELECT `id` FROM `privileges_buy` WHERE `server`="' . $id . '" AND `status`="1" LIMIT 1');
            if (!$sql->num()) {
                unset($servers[$i]);
            }
        }

        if (!count($servers)) {
            return null;
        }

        $sql->query('SELECT `unit` FROM `servers` WHERE `id`="' . end($servers) . '" LIMIT 1');
        $server = $sql->get();

        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        include(LIB . 'ssh.php');

        // Открываем ssh соединение на сервере
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            return 'Ошибка: UNIT #' . $server['unit'] . ' не удалось установить соединение.';
        }

        $time = $start_point - 172800;

        foreach ($servers as $id) {
            $sql->query('DELETE FROM `privileges_buy` WHERE `date`<"' . $time . '" AND status`="0" LIMIT 5');

            $aMail = [];

            $sql->query('SELECT `uid`, `tarif` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
            $server = $sql->get();

            $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
            $tarif = $sql->get();

            $file = $tarif['install'] . $server['uid'] . '/' . cron::$admins_file[$game];

            $text = $ssh->get('cat ' . $file);

            $privileges = $sql->query('SELECT `id`, `text`, `sql`, `mail` FROM `privileges_buy` WHERE `server`="' . $id . '" AND `status`="1" LIMIT 3');
            while ($privilege = $sql->get($privileges)) {
                $text .= base64_decode($privilege['text']) . PHP_EOL;

                $sql->query(base64_decode($privilege['sql']));
                $sql->query('DELETE FROM `privileges_buy` WHERE `id`="' . $privilege['id'] . '" LIMIT 1');

                $aMail[] = $privilege['mail'];
            }

            $temp = System::temp($text);

            $ssh->setfile($temp, $file);
            $ssh->set('chmod 0500' . ' ' . $file);

            unlink($temp);

            $cmd = $game == 'cs' ? 'amx_reloadadmins' : 'sm_reloadadmins';

            $ssh->set('chown server' . $server['uid'] . ':servers ' . $file);
            $ssh->set("sudo -u server" . $server['uid'] . " tmux send-keys -t s_" . $server['uid'] . " \"" . $cmd . "\" C-m");

            foreach ($aMail as $mail) {
                System::mail('Успешная привилегия', System::text('mail', 'success_privilege'), $mail);
            }

            echo 'server #' . $id . ' (' . $game . ') -> add privileges ' . PHP_EOL;
        }

        return null;
    }
}
