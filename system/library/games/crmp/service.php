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
use EngineGP\Model\Game;
use EngineGP\Model\Parameters;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class service
{
    public static function buy($aData = [])
    {
        global $cfg, $sql, $user, $start_point;

        // Проверка локации
        $sql->query('SELECT `address`, `test` FROM `units` WHERE `id`="' . $aData['unit'] . '" AND `crmp`="1" AND `show`="1" LIMIT 1');
        if (!$sql->num()) {
            System::outjs(['e' => 'Локация не найдена.']);
        }

        $unit = $sql->get();

        // Проверка тарифа
        $sql->query('SELECT `id` FROM `tarifs` WHERE `id`="' . $aData['tarif'] . '" AND `unit`="' . $aData['unit'] . '" AND `show`="1" LIMIT 1');
        if (!$sql->num()) {
            System::outjs(['e' => 'Тариф не найден.']);
        }

        $sql->query('SELECT '
            . '`slots_min`,'
            . '`slots_max`,'
            . '`port_min`,'
            . '`port_max`,'
            . '`hostname`,'
            . '`packs`,'
            . '`time`,'
            . '`test`,'
            . '`tests`,'
            . '`discount`,'
            . '`ftp`,'
            . '`plugins`,'
            . '`console`,'
            . '`stats`,'
            . '`copy`,'
            . '`web`,'
            . '`plugins_install`,'
            . '`cpu`,'
            . '`ram`,'
            . '`hdd`,'
            . '`autostop`,'
            . '`ip`,'
            . '`price`'
            . ' FROM `tarifs` WHERE `id`="' . $aData['tarif'] . '" LIMIT 1');

        $tarif = $sql->get();

        // Проверка сборки
        if (!array_key_exists($aData['pack'], System::b64djs($tarif['packs'], true))) {
            System::outjs(['e' => 'Сборка не найдена.']);
        }

        $test = 0;

        // Проверка периода на тест
        if ($aData['test']) {
            if (!$tarif['test'] || !$unit['test']) {
                System::outjs(['e' => 'Тестовый период недоступен.']);
            }


            // Проверка на повторный запрос
            $sql->query('SELECT `id`, `game` FROM `tests` WHERE `user`="' . $user['id'] . '" LIMIT 1');
            if ($sql->num()) {
                $test_info = $sql->get();

                if (!$cfg['tests']['game'] || $test_info['game'] == 'crmp') {
                    System::outjs(['e' => 'Тестовый период предоставляется один раз.']);
                }

                $sql->query('SELECT `id` FROM `servers` WHERE `user`="' . $user['id'] . '" AND `test`="1" LIMIT 1');
                if ($sql->num() and !$cfg['tests']['sametime']) {
                    System::outjs(['e' => 'Чтобы получить тестовый период другой игры, дождитесь окончания текущего.']);
                }
            }

            // Проверка наличия мест на локации
            $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $aData['unit'] . '" AND `test`="1" AND `time`>"' . $start_point . '" LIMIT ' . $unit['test']);
            if ($sql->num() == $unit['test']) {
                System::outjs(['e' => 'Свободного места для тестового периода нет.']);
            }

            // Проверка наличия мест для выбранного тарифа
            $sql->query('SELECT `id` FROM `servers` WHERE `tarif`="' . $aData['tarif'] . '" AND `test`="1" AND `time`>"' . $start_point . '" LIMIT ' . $tarif['tests']);
            if ($sql->num() == $tarif['tests']) {
                System::outjs(['e' => 'Свободного места для тестового периода выбранного тарифа нет.']);
            }

            $test = 1;
        } elseif // Проверка периода
        (!$cfg['settlement_period'] and !in_array($aData['time'], explode(':', $tarif['time']))) {
            System::outjs(['e' => 'Переданные данные периода неверны.']);
        }

        // Проверка слот
        if ($aData['slots'] < $tarif['slots_min'] || $aData['slots'] > $tarif['slots_max']) {
            System::outjs(['e' => 'Переданные данные слот неверны.']);
        }

        // Определение суммы
        if ($cfg['settlement_period']) {
            // Цена аренды за расчетный период
            $sum = Game::define_sum($tarif['discount'], $tarif['price'], $aData['slots'], $start_point);

            $aData['time'] = Game::define_period('buy', Parameters::$aDayMonth);
        } else {
            $sum = Game::define_sum($tarif['discount'], $tarif['price'], $aData['slots'], $aData['time']);
        }

        // Проверка промо-кода
        $promo = Game::define_promo(
            $aData['promo'],
            $tarif['discount'],
            $sum,
            [
                'tarif' => $aData['tarif'],
                'slots' => $aData['slots'],
                'time' => $aData['time'],
                'user' => $user['id'],
            ]
        );

        $days = $aData['time']; // Кол-во дней аренды

        // Использование промо-кода
        if (is_array($promo)) {
            if (array_key_exists('sum', $promo)) {
                $sum = $promo['sum'];
            } else {
                $days += $promo['days'];
            } // Кол-во дней аренды с учетом подарочных (промо-код)
        }

        // Проверка баланса
        if ($user['balance'] < $sum) {
            System::outjs(['e' => 'У вас не хватает ' . (round($sum - $user['balance'], 2)) . ' ' . $cfg['currency']]);
        }

        // Выделенный адрес игрового сервера
        if (!empty($tarif['ip'])) {
            $aIp = explode(':', $tarif['ip']);

            $ip = false;
            $port = Parameters::$aDefPort['crmp'];

            // Проверка наличия свободного адреса
            foreach ($aIp as $adr) {
                $adr = trim($adr);

                $sql->query('SELECT `id` FROM `servers` WHERE `unit`="' . $aData['unit'] . '" AND `address` LIKE "' . $adr . ':%" LIMIT 1');
                if (!$sql->num()) {
                    $ip = $adr;

                    break;
                }
            }
        } else {
            $ip = System::first(explode(':', $unit['address']));
            $port = false;

            // Проверка наличия свободных портов для сервера, query и rcon
            for ($portMin = $tarif['port_min']; $portMin <= $tarif['port_max']; $portMin++) {
                // Проверка порта для сервера
                $sql->query('SELECT `id` FROM `servers` 
                 WHERE `unit`="' . $aData['unit'] . '" 
                 AND (
                     `port`="' . $portMin . '" OR
                     `port_query`="' . $portMin . '" OR
                     `port_rcon`="' . $portMin . '"
                 ) 
                 LIMIT 1');

                if (!$sql->num()) {
                    $port = $portMin;
                    $port_query = $portMin;
                    $port_rcon = $portMin;
                    break;
                }
            }
        }

        if (!$ip || !$port) {
            $sql->query('UPDATE `tarifs` set `show`="0" WHERE `id`="' . $aData['tarif'] . '" LIMIT 1');

            System::outjs(['e' => 'К сожалению нет доступных мест, обратитесь в тех.поддержку.']);
        }

        if ($test) {
            $aData['time'] = Game::time($start_point, $tarif['test']);
        } else {
            $aData['time'] = Game::time($start_point, $days);
        }

        // Массив данных
        $aSDATA = [
            'unit' => $aData['unit'], // идентификатор локации
            'tarif' => $aData['tarif'], // идентификатор тарифа
            'pack' => $aData['pack'], // Выбранная сборка для установки
            'time' => $aData['time'], // Время аренды
            'days' => $days, // Число дней
            'sum' => $sum, // Сумма списания
            'test' => $test, // тестовый период
            'address' => $ip, // адрес игрового сервера
            'port' => $port, // порт игрового сервера
            'port_query' => $port_query, // порт для проверки query
            'port_rcon' => $port_rcon, // порт для подключения по rcon
            'slots' => $aData['slots'], // Кол-во слот
            'autostop' => $tarif['autostop'], // Выключение при 0 онлайне
            'ftp' => $tarif['ftp'], // Использование ftp
            'plugins' => $tarif['plugins'], // Использование плагинов
            'console' => $tarif['console'], // Использование консоли
            'stats' => $tarif['stats'], // Использование графиков (ведение статистики)
            'copy' => $tarif['copy'], // Использование резервных копий
            'web' => $tarif['web'], // Использование доп услуг
            'plugins_install' => $tarif['plugins_install'], // Список установленных плагинов
            'cpu' => $tarif['cpu'], // значение cpu
            'ram' => $tarif['ram'], // значение ram
            'hdd' => $tarif['hdd'], // Дисковое пространство
            'promo' => $promo, // Использование промо-кода
        ];

        return $aSDATA;
    }

    public static function install($aSDATA = [])
    {
        global $cfg, $sql, $user, $start_point;

        include(LIB . 'ssh.php');

        // Массив данных локации (адрес,пароль)
        $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $aSDATA['unit'] . '" LIMIT 1');
        $unit = $sql->get();

        // Проверка ssh соединения с локацией
        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            System::outjs(['e' => System::text('error', 'ssh')]);
        }

        // Массив данных тарифа (путь сборки,путь установки)
        $sql->query('SELECT `path`, `install`, `hostname` FROM `tarifs` WHERE `id`="' . $aSDATA['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Получение идентификаторов игрового сервера
        $sql->query('INSERT INTO `servers` set uid="1"');
        $id = $sql->id();
        $uid = $id + 1000;

        // Директория сборки
        $path = $tarif['path'] . $aSDATA['pack'];

        // Директория игрового сервера
        $install = $tarif['install'] . $uid;

        $ssh->set('mkdir ' . $install . ';' // Создание директории
            . 'useradd -s /bin/false -d ' . $install . ' -g servers -u ' . $uid . ' server' . $uid . ';' // Создание пользователя сервера на локации
            . 'chown server' . $uid . ':servers ' . $install . ';' // Изменение владельца и группы директории
            . 'cd ' . $install . ' && sudo -u server' . $uid . ' tmux new-session -ds i_' . $uid . ' sh -c "cp -r ' . $path . '/. .;' // Копирование файлов сборки для сервера
            . 'find . -type d -exec chmod 700 {} \;;'
            . 'find . -type f -exec chmod 777 {} \;;'
            . 'chmod 500 ' . Parameters::$aFileGame['crmp'] . '"');

        // Запись данных нового сервера
        $sql->query('UPDATE `servers` set
				`uid`="' . $uid . '",
				`unit`="' . $aSDATA['unit'] . '",
				`tarif`="' . $aSDATA['tarif'] . '",
				`user`="' . $user['id'] . '",
				`address`="' . $aSDATA['address'] . '",
				`port`="' . $aSDATA['port'] . '",
				`port_query`="' . $aSDATA['port_query'] . '",
				`port_rcon`="' . $aSDATA['port_rcon'] . '",
				`game`="crmp",
				`slots`="' . $aSDATA['slots'] . '",
				`slots_start`="' . $aSDATA['slots'] . '",
				`status`="install",
				`name`="' . $tarif['hostname'] . '",
				`pack`="' . $aSDATA['pack'] . '",
				`plugins_use`="' . $aSDATA['plugins'] . '",
				`ftp_use`="' . $aSDATA['ftp'] . '",
				`console_use`="' . $aSDATA['console'] . '",
				`stats_use`="' . $aSDATA['stats'] . '",
				`copy_use`="' . $aSDATA['copy'] . '",
				`web_use`="' . $aSDATA['web'] . '",
				`vac`="1",
				`cpu`="' . $aSDATA['cpu'] . '",
				`ram`="' . $aSDATA['ram'] . '",
				`hdd`="' . $aSDATA['hdd'] . '",
				`time`="' . $aSDATA['time'] . '",
				`date`="' . $start_point . '",
				`test`="' . $aSDATA['test'] . '",
				`map_start`="' . System::passwd(8) . '",
				`autostop`="' . $aSDATA['autostop'] . '" WHERE `id`="' . $id . '" LIMIT 1');

        // Запись установленных плагинов
        if ($aSDATA['plugins']) {
            // Массив идентификаторов плагинов
            $aPlugins = System::b64djs($aSDATA['plugins_install']);

            if (isset($aPlugins[$aSDATA['pack']])) {
                $plugins = explode(',', $aPlugins[$aSDATA['pack']]);

                foreach ($plugins as $plugin) {
                    if ($plugin) {
                        $sql->query('INSERT INTO `plugins_install` set `server`="' . $id . '", `plugin`="' . $plugin . '", `time`="' . $start_point . '"');
                    }
                }
            }
        }

        // Списание средств с баланса пользователя
        $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] - $aSDATA['sum']) . '" WHERE `id`="' . $user['id'] . '" LIMIT 1');

        // Запись получения тестового периода
        if ($aSDATA['test']) {
            $sql->query('INSERT INTO `tests` set `server`="' . $id . '", `unit`="' . $aSDATA['unit'] . '", `game`="crmp", `user`="' . $user['id'] . '", `time`="' . $start_point . '"');
            $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(System::text('logs', 'buy_server_test'), ['id' => $id]) . '", `date`="' . $start_point . '", `type`="buy", `money`="0"');
        } else {
            // Реф. система
            Game::part($user['id'], $aSDATA['sum']);

            // Запись логов
            if (!is_array($aSDATA['promo'])) {
                $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(System::text('logs', 'buy_server'), ['days' => Game::parse_day($aSDATA['days'], true), 'money' => $aSDATA['sum'], 'id' => $id]) . '", `date`="' . $start_point . '", `type`="buy", `money`="' . $aSDATA['sum'] . '"');
            } else {
                $sql->query('UPDATE `servers` set `benefit`="' . $aSDATA['time'] . '" WHERE `id`="' . $id . '" LIMIT 1');
                $sql->query('INSERT INTO `promo_use` set `promo`="' . $aSDATA['promo']['id'] . '", `user`="' . $user['id'] . '", `time`="' . $start_point . '"');
                $sql->query('INSERT INTO `logs` set `user`="' . $user['id'] . '", `text`="' . System::updtext(System::text('logs', 'buy_server_promo'), ['days' => Game::parse_day($aSDATA['days'], true), 'money' => $aSDATA['sum'], 'promo' => $aSDATA['promo']['cod'], 'id' => $id]) . '", `date`="' . $start_point . '", `type`="buy", `money`="' . $aSDATA['sum'] . '"');
            }
        }

        return $id;
    }
}
