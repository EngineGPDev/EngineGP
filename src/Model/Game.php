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

namespace EngineGP\Model;

use EngineGP\System;
use EngineGP\Infrastructure\GeoIP\SxGeo;

class Game
{
    public static function parse_day($days, $lower = false)
    {
        $aText = ['День', 'Дня', 'Дней'];

        if ($lower) {
            $aText = ['день', 'дня', 'дней'];
        }

        return System::date_decl($days, $aText);
    }

    public static function time($time, $days)
    {
        return $days * 86400 + $time;
    }

    public static function country($name)
    {
        global $cfg;

        if (file_exists(TPL . '/images/country/' . $name . '.png')) {
            return $cfg['url'] . 'template/images/country/' . $name . '.png';
        }

        return $cfg['url'] . 'template/images/country/none.png';
    }

    public static function determine($status, $go = false, $tpl = 'content')
    {
        global $html, $text;

        if (!in_array($status, ['install', 'reinstall', 'update', 'recovery', 'overdue', 'blocked'])) {
            return true;
        }

        $aText = [
            'install' => 'устанавливается',
            'reinstall' => 'переустанавливается',
            'update' => 'обновляется',
            'recovery' => 'восстанавливается',
            'overdue' => 'не оплачен',
            'blocked' => 'заблокирован',
        ];

        $msg = System::updtext(System::text('servers', 'determine'), ['status' => $aText[$status]]);

        if ($go) {
            System::out($msg);
        }

        $html->get('informer');

        $html->set('[class]', 'info_red');
        $html->set('[text]', $msg);

        $html->pack($tpl);

        return false;
    }

    public static function crontab_week($week)
    {
        $aWeek = [];
        $aWeek[1] = isset($week['\'1\'']) ? 'Пн., ' : '';
        $aWeek[2] = isset($week['\'2\'']) ? 'Вт., ' : '';
        $aWeek[3] = isset($week['\'3\'']) ? 'Ср., ' : '';
        $aWeek[4] = isset($week['\'4\'']) ? 'Чт., ' : '';
        $aWeek[5] = isset($week['\'5\'']) ? 'Пт., ' : '';
        $aWeek[6] = isset($week['\'6\'']) ? 'Сб., ' : '';
        $aWeek[7] = isset($week['\'7\'']) ? 'Вс., ' : '';

        $days = '';

        foreach ($aWeek as $index => $val) {
            if ($val == '') {
                continue;
            }

            $days .= $val;
        }

        $days = substr($days, 0, -2);

        if ($days == '') {
            $days = 'Пн., Вт., Ср., Чт., Пт., Сб., Вс.';
        }

        return $days;
    }

    public static function crontab_time($allhour, $hour, $minute)
    {
        if ($allhour) {
            return 'Каждый час';
        }

        $aHour = [
            '00', '01', '02',
            '03', '04', '05',
            '06', '07', '08',
            '09', '10', '11',
            '12', '13', '14',
            '15', '16', '17',
            '18', '19', '20',
            '21', '22', '23',
        ];

        $aMinute = [
            '00', '05', '10',
            '15', '20', '25',
            '30', '35', '40',
            '45', '50', '55',
        ];

        if (!in_array($hour, $aHour)) {
            $hour = '00';
        }

        if (!in_array($minute, $aMinute)) {
            $minute = '00';
        }

        return $hour . ':' . $minute;
    }

    public static function crontab($id, $cid, $data = [])
    {
        global $cfg;

        if ($data['allhour']) {
            $time = '0 * * * ';
        } else {
            $hour = [
                '00', '01', '02',
                '03', '04', '05',
                '06', '07', '08',
                '09', '10', '11',
                '12', '13', '14',
                '15', '16', '17',
                '18', '19', '20',
                '21', '22', '23',
            ];

            $minute = [
                '00', '05', '10',
                '15', '20', '25',
                '30', '35', '40',
                '45', '50', '55',
            ];

            if (!in_array($data['hour'], $hour)) {
                $data['hour'] = '00';
            }

            if (!in_array($data['minute'], $minute)) {
                $data['minute'] = '00';
            }

            $time = $data['minute'] . ' ' . $data['hour'] . ' * * ';
        }

        $week = [];
        $week[1] = isset($data['week']['\'1\'']) ? 1 : 0;
        $week[2] = isset($data['week']['\'2\'']) ? 2 : 0;
        $week[3] = isset($data['week']['\'3\'']) ? 3 : 0;
        $week[4] = isset($data['week']['\'4\'']) ? 4 : 0;
        $week[5] = isset($data['week']['\'5\'']) ? 5 : 0;
        $week[6] = isset($data['week']['\'6\'']) ? 6 : 0;
        $week[7] = isset($data['week']['\'7\'']) ? 7 : 0;

        $check = 0;

        foreach ($week as $index => $val) {
            $check += $val;
        }

        if ($check == 28 || !$check) {
            $week = '*';
        } else {
            $weeks = $week[1] . ',' . $week[2] . ',' . $week[3] . ',' . $week[4] . ',' . $week[5] . ',' . $week[6] . ',' . $week[7];
            $weeks = str_replace([',0', '0'], '', $weeks);
            $week = $weeks[0] == ',' ? substr($weeks, 1) : $weeks;
        }

        $cron_task = $time . $week . ' tmux new-session -ds s' . $id . ' bash -c \'cd /var/www/enginegp && php cron.php ' . $cfg['cron_key'] . ' server_cron ' . $id . ' ' . $cid . '\'';

        return $cron_task;
    }

    public static function parse_tarif($aTarif = [], $aUnit = [])
    {
        global $cfg, $mcache;

        $nmch = 'parse_tarif_' . $aTarif['id'];

        $cache = $mcache->get($nmch);

        if (is_array($cache)) {
            return $cache;
        }

        if (isset($aTarif['fps'])) {
            $aFPS = explode(':', $aTarif['fps']);
        }

        if (isset($aTarif['tickrate'])) {
            $aTICKRATE = explode(':', $aTarif['tickrate']);
        }

        if (isset($aTarif['ram'])) {
            $aRAM = explode(':', $aTarif['ram']);
        }

        $fps = '';

        if (isset($aFPS)) {
            foreach ($aFPS as $value) {
                $fps .= '<option value="' . $value . '">' . $value . ' FPS</option>';
            }
        }

        $tickrate = '';

        if (isset($aTICKRATE)) {
            foreach ($aTICKRATE as $value) {
                $tickrate .= '<option value="' . $value . '">' . $value . ' TickRate</option>';
            }
        }

        $ram = '';

        if (isset($aRAM)) {
            foreach ($aRAM as $value) {
                $ram .= '<option value="' . $value . '">' . $value . ' RAM</option>';
            }
        }

        $packs = '';
        $aPack = System::b64djs($aTarif['packs'], true);

        if (is_array($aPack)) {
            foreach ($aPack as $index => $name) {
                $packs .= '<option value="' . $index . '">' . $name . '</option>';
            }
        }

        $slots = '';

        for ($i = $aTarif['slots_min']; $i <= $aTarif['slots_max']; $i += 1) {
            $slots .= '<option value="' . $i . '">' . $i . ' шт.</option>';
        }

        $aTime = explode(':', $aTarif['time']);

        $time = Game::parse_time($aTarif['discount'], $aTarif['id'], $aTime);

        if ($aTarif['test'] and $aUnit['test']) {
            $time .= '<option value="test">Тестовый период ' . Game::parse_day($aTarif['test']) . '</option>';
        }

        $data = [
            'packs' => $packs,
            'slots' => $slots,
            'time' => $time,
            'fps' => $fps,
            'tickrate' => $tickrate,
            'ram' => $ram,
        ];

        $mcache->set($nmch, $data, false, 60);

        return $data;
    }

    public static function parse_time($discount, $tarif, $aTime = [], $type = 'buy')
    {
        global $cfg;

        $time = '';

        $arr = isset(Parameters::$disconunt['service'][$tarif]) ? $tarif : 'time';

        foreach ($aTime as $value) {
            if (array_key_exists($value, Parameters::$disconunt['service'][$arr][$type]) and $discount) {
                $data = explode(':', Parameters::$disconunt['service'][$arr][$type][$value]);

                // Если наценка
                if ($data[0] == '+') {
                    // Если значение в процентах
                    if (substr($data[1], -1) == '%') {
                        $time .= '<option value="' . $value . '">' . Game::parse_day($value) . ' (Наценка ' . $data[1] . ')</option>';
                    } else {
                        $time .= '<option value="' . $value . '">' . Game::parse_day($value) . ' (Наценка ' . System::int($data[1]) . ' ' . $cfg['currency'] . ')</option>';
                    }
                } else {
                    // Если значение в процентах
                    if (substr($data[1], -1) == '%') {
                        $time .= '<option value="' . $value . '">' . Game::parse_day($value) . ' (Скидка ' . $data[1] . ')</option>';
                    } else {
                        $time .= '<option value="' . $value . '">' . Game::parse_day($value) . ' (Скидка ' . System::int($data[1]) . ' ' . $cfg['currency'] . ')</option>';
                    }
                }
            } else {
                $time .= '<option value="' . $value . '">' . Game::parse_day($value) . '</option>';
            }
        }

        return $time;
    }

    public static function define_period($type, $aD_M, $time = 0)
    {
        global $start_point;

        if ($time < $start_point) {
            $time = $start_point;
        }

        $day = $type == 'extend' ? date('d', $time) : date('d', $start_point);
        $month = $type == 'extend' ? date('n', $time) : date('n', $start_point);

        $period = $aD_M[$month] - $day;

        if ($day > 15) {
            $period += $month != 12 ? $aD_M[$month + 1] : $aD_M[1];
        }

        return $period + 1;
    }

    public static function define_sum($discount, $price, $slots, $time, $type = 'buy')
    {
        global $sql, $user, $cfg, $start_point;

        if ($cfg['settlement_period']) {
            if ($time < $start_point) {
                $time = $start_point;
            }

            $day = $type == 'extend' ? date('d', $time) : date('d', $start_point);
            $month = $type == 'extend' ? date('n', $time) : date('n', $start_point);

            $period = Parameters::$aDayMonth[$month] + 1 - $day;

            $new_month_sum = 0;

            if ($day > 15) {
                $new_month_sum = ceil($price * $slots);
            }

            $sum = Parameters::$aDayMonth[$month] == $period ? $price * $slots : floor($price * $slots / 30 * $period) + $new_month_sum;
        } else {
            $sum = floor($price * $slots / 30 * $time);

            if (array_key_exists($time, Parameters::$disconunt['service']['time'][$type]) and $discount) {
                $data = explode(':', Parameters::$disconunt['service']['time'][$type][$time]);

                // Если наценка
                if ($data[0] == '+') {
                    // Если значение в процентах
                    if (substr($data[1], -1) == '%') {
                        $sum = ceil($sum + $sum / 100 * intval($data[1]));
                    } else {
                        $sum = $sum + intval($data[1]);
                    }
                } else {
                    // Если значение в процентах
                    if (substr($data[1], -1) == '%') {
                        $sum = ceil($sum - $sum / 100 * intval($data[1]));
                    } else {
                        $sum = $sum - intval($data[1]);
                    }
                }
            }
        }

        // Проверяем, что пользователь авторизован перед выполнением операций со скидкой
        if ($user['id'] !== null) {
            $sel = $type == 'buy' ? 'rental' : 'extend';

            $sql->query('SELECT `' . $sel . '` FROM `users` WHERE `id`="' . $user['id'] . '" LIMIT 1');
            $user = array_merge($user, $sql->get());

            $sum = strpos($user[$sel], '%') ? $sum - $sum / 100 * floatval($user[$sel]) : $sum - floatval($user[$sel]);
        }

        if ($sum < 0) {
            System::outjs(['e' => 'Ошибка: сумма за услугу неверна']);
        }

        return $sum;
    }

    public static function define_promo($cod, $discount, $sum, $data = [], $type = 'buy')
    {
        global $cfg, $sql, $go, $start_point;

        // Проверка формата кода
        if (System::valid($cod, 'promo')) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код имеет неверный формат.']);
            }

            return null;
        }

        $sql->query('SELECT `id`, `value`, `discount`, `data`, `hits`, `use`, `extend`, `user`, `server` FROM `promo` WHERE `cod`="' . $cod . '" AND `tarif`="' . $data['tarif'] . '" AND `time`>"' . $start_point . '" LIMIT 1');

        // Проверка наличия промо-кода
        if (!$sql->num()) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код не найден.']);
            }

            return null;
        }

        $promo = $sql->get();

        // Проверка типа при аренде
        if ($type == 'buy' and $promo['extend']) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код для продления игрового сервера.']);
            }

            return null;
        }

        // Проверка типа при продлении
        if ($type != 'buy' and !$promo['extend']) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код для аренды нового игрового сервера.']);
            }

            return null;
        }

        // Проверка доступности на пользователя
        if ($promo['user'] and $data['user'] != $promo['user']) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код не найден.']);
            }

            return null;
        }

        // Проверка доступности на сервер
        if ($promo['server'] and $data['server'] != $promo['server']) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код не найден.']);
            }

            return null;
        }

        $use = $promo['use'] < 1 ? '1' : $promo['use'];

        // Проверка доступности
        $sql->query('SELECT `id` FROM `promo_use` WHERE `promo`="' . $promo['id'] . '" LIMIT ' . $use);
        if ($sql->num() >= $promo['use']) {
            if (!$go) {
                System::outjs(['e' => 'Промо-код использован максимальное количество раз.']);
            }

            return null;
        }

        // Данные для сравнения
        $data_promo = System::b64djs($promo['data'], true);

        $check = 0;

        // Проверка периода
        if (isset($data['time']) && isset($data_promo['time']) && in_array($data['time'], explode(':', $data_promo['time']))) {
            $check = 1;
        }

        // Проверка значения FPS
        if ((isset($data['fps']) and isset($data_promo['fps'])) and in_array($data['fps'], explode(':', $data_promo['fps']))) {
            $check += 1;
        }

        // Проверка значения TICKRATE
        if ((isset($data['tickrate']) and isset($data_promo['tickrate'])) and in_array($data['tickrate'], explode(':', $data_promo['tickrate']))) {
            $check += 1;
        }

        // Проверка значения RAM
        if ((isset($data['ram']) and isset($data_promo['ram'])) and in_array($data['ram'], explode(':', $data_promo['ram']))) {
            $check += 1;
        }

        //	Проверка кол-ва слот
        if (isset($data_promo['slots'])) {
            // Если совпало по перечислению слот (через число:число:число ...)
            if (in_array($data['slots'], explode(':', $data_promo['slots']))) {
                $check += 1;
            } else {
                // Если указан диапозон слот
                $aSl = explode('-', $data_promo['slots']);
                if (count($aSl) == 2 and ($data['slots'] >= $aSl[0] and $data['slots'] <= $aSl[1])) {
                    $check += 1;
                }
            }
        }

        // Проверка совпадений
        if ($check < $promo['hits']) {
            if (!$go) {
                System::outjs(['e' => 'Условия для данного промо-кода не выполнены.']);
            }

            return null;
        }

        // Если скидка
        if ($promo['discount']) {
            // Если не суммировать скидки
            if (!$cfg['promo_discount']) {
                if (array_key_exists($data['time'], Parameters::$disconunt['service']['time'][$type]) and $discount) {
                    $data = explode(':', Parameters::$disconunt['service']['time'][$type][$data['time']]);

                    // Если скидка
                    if ($data[0] == '-') {
                        // Если значение в процентах
                        if (substr($data[1], -1) == '%') {
                            $sum = ceil($sum + $sum / 100 * intval($data[1]));
                        } else {
                            $sum = $sum + intval($data[1]);
                        }
                    }
                }
            }

            // Пересчет суммы
            if (substr($promo['value'], -1) == '%') {
                $sum = $sum - ceil($sum / 100 * intval($promo['value']));
            } else {
                $sum = $sum - intval($promo['value']);
            }

            if (!$go) {
                System::outjs(['sum' => $sum, 'discount' => 1, 'cur' => $cfg['currency']]);
            }

            return ['id' => $promo['id'], 'cod' => $cod, 'sum' => $sum];

        }

        // Подарочные дни
        $days = intval($promo['value']);

        if (!$go) {
            System::outjs(['days' => Game::parse_day($days)]);
        }

        return ['id' => $promo['id'], 'cod' => $cod, 'days' => $days];
    }

    public static function info_tarif($game, $tarif, $param)
    {
        if ($game == 'cs') {
            return $tarif . ' / ' . $param['fps'] . ' FPS';
        }

        if ($game == 'mc') {
            return $tarif . ' / ' . $param['ram'] . ' RAM';
        }

        if ($game == 'cssold') {
            return $tarif . ' / ' . $param['fps'] . ' FPS / ' . $param['tickrate'] . ' TickRate';
        }

        if (in_array($game, ['css', 'csgo', 'cs2', 'rust'])) {
            return $tarif . ' / ' . $param['tickrate'] . ' TickRate';
        }

        return $tarif;
    }

    public static function maplist($id, $unit, $folder, $map, $go, $mcache = '')
    {
        global $user, $sql;

        include(LIB . 'ssh.php');

        if (!$ssh->auth($unit['passwd'], $unit['address'])) {
            if ($go) {
                System::outjs(['e' => System::text('error', 'ssh')], $mcache);
            }

            System::outjs(['maps', '<option value="0">unknown</option>']);
        }

        // Генерация списка карт
        $aMaps = array_diff(explode("\n", $ssh->get('cd ' . $folder . ' && du -ah | grep -e "\.bsp$" -e "\.vpk$" | awk \'{print $2}\'')), ['']);

        // Удаление ".bsp" или ".vpk"
        $aMaps = str_ireplace(['./', '.bsp', '.vpk'], '', $aMaps);

        if ($go) {
            $map = str_replace('|', '/', urldecode($map));

            // Проверка наличия выбранной карты
            if (in_array($map, $aMaps)) {
                $sql->query('UPDATE `servers` set `map_start`="' . $map . '" WHERE `id`="' . $id . '" LIMIT 1');
            }

            System::outjs(['s' => 'ok'], $mcache);
        }

        sort($aMaps);
        reset($aMaps);

        $ismap = in_array($map, $aMaps);
        $maps = $ismap ? '<option value="' . str_replace('/', '|', $map) . '">' . $map . '</option>' : '<option value="">Указанная ранее карта "' . $map . '" не найдена</option>';

        // Удаление стартовой карты
        if ($ismap) {
            unset($aMaps[array_search($map, $aMaps)]);
        }

        foreach ($aMaps as $map) {
            $maps .= '<option value="' . str_replace('/', '|', $map) . '">' . $map . '</option>';
        }

        System::outjs(['maps' => $maps]);
    }

    public static function owners($aRights)
    {
        if (array_search(0, $aRights)) {
            return 'Есть ограничения в доступе.';
        }

        return 'Выданы все права.';
    }

    public static function part($uid, $money)
    {
        global $cfg, $sql, $start_point;

        if ($cfg['part']) {
            return null;
        }

        $sql->query('SELECT `part` FROM `users` WHERE `id`="' . $uid . '" LIMIT 1');
        $user = $sql->get();

        if (!$user['part']) {
            return null;
        }

        $sql->query('SELECT `balance`, `part_money` FROM `users` WHERE `id`="' . $user['part'] . '" LIMIT 1');
        if (!$sql->num()) {
            return null;
        }

        $user = array_merge($user, $sql->get());

        $sum = round($money / 100 * $cfg['part_proc'], 2);

        if ($cfg['part_money']) {
            $sql->query('UPDATE `users` set `part_money`="' . ($user['part_money'] + $sum) . '" WHERE `id`="' . $user['part'] . '" LIMIT 1');
        } else {
            $sql->query('UPDATE `users` set `balance`="' . ($user['balance'] + $sum) . '" WHERE `id`="' . $user['part'] . '" LIMIT 1');
        }

        $sql->query('INSERT INTO `logs` set `user`="' . $user['part'] . '", `text`="' . System::updtext(
            System::text('logs', 'part'),
            ['part' => $uid, 'money' => $sum]
        ) . '", `date`="' . $start_point . '", `type`="part", `money`="' . $sum . '"');

        return null;
    }

    public static function map($map, $aMaps)
    {
        if (!is_array($aMaps)) {
            $aMaps = explode("\n", str_ireplace(['./', '.bsp', '.vpk'], '', $aMaps));
        }

        if (in_array($map, $aMaps)) {
            return false;
        }

        return true;
    }

    public static function mapsql($arr = [])
    {
        $sql = 'AND (';

        foreach ($arr as $map) {
            $sql .= ' `name` LIKE "' . $map . '_%" OR';
        }

        return $sql == 'AND (' ? '' : substr($sql, 0, -3) . ')';
    }

    public static function iptables_whois($mcache)
    {
        $address = isset($_POST['address']) ? trim($_POST['address']) : System::outjs(['info' => 'Не удалось получить информацию.'], $mcache);

        if (System::valid($address, 'ip')) {
            System::outjs(['e' => System::text('servers', 'firewall')], $mcache);
        }

        $SxGeo = new SxGeo(DATA . 'SxGeoCity.dat');

        $data = $SxGeo->getCityFull($address);

        $info = 'Информация об IP адресе:';

        if ($data['country']['name_ru'] != '') {
            $info .= '<p>Страна: ' . $data['country']['name_ru'];

            if ($data['city']['name_ru'] != '') {
                $info .= '<p>Город: ' . $data['city']['name_ru'];
            }

            $info .= '<p>Подсеть: ' . System::whois($address);

        } else {
            $info = 'Не удалось получить информацию.';
        }

        System::outjs(['info' => $info], $mcache);
    }

    public static function iptables($id, $action, $source, $ip, $port, $unit, $snw = false, $ssh = false)
    {
        global $cfg, $sql, $start_point;

        if (!$ssh) {
            $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $unit . '" LIMIT 1');
            $unit = $sql->get();

            include(LIB . 'ssh.php');

            if (!$ssh->auth($unit['passwd'], $unit['address'])) {
                return ['e' => System::text('all', 'ssh')];
            }
        }

        switch ($action) {
            case 'block':
                if (System::valid($source, 'ip')) {
                    return ['e' => System::text('servers', 'firewall')];
                }

                // Если подсеть
                if ($snw) {
                    $source = System::whois($source);

                    if ($source == 'не определена') {
                        return ['e' => 'Не удалось определить подсеть для указанного адреса.'];
                    }
                }

                $sql->query('SELECT `id` FROM `firewall` WHERE `sip`="' . $source . '" AND `server`="' . $id . '" LIMIT 1');

                // Если такое правило уже добавлено или указан адрес сайта (ПУ)
                if ($sql->num() || ($source == $cfg['ip'] || $source == $cfg['subnet'])) {
                    return ['s' => 'ok'];
                }

                $sql->query('INSERT INTO `firewall` set `sip`="' . $source . '", `dest`="' . $ip . ':' . $port . '", `server`="' . $id . '", `time`="' . $start_point . '"');

                $line = $sql->id();

                $rule = 'iptables -I INPUT -s ' . $source . ' -p udp -d ' . $ip . ' --dport ' . $port . ' -j DROP;';

                $ssh->set($rule . ' echo -e "#' . $line . ';\n' . $rule . '" >> /root/' . $cfg['iptables']);

                return ['s' => 'ok'];

            case 'unblock':
                if (!is_numeric($source) and System::valid($source, 'ip')) {
                    return ['e' => System::text('servers', 'firewall')];
                }

                if (is_numeric($source)) {
                    $sql->query('SELECT `id`, `sip` FROM `firewall` WHERE `id`="' . $source . '" AND `server`="' . $id . '" LIMIT 1');

                    // Если такое правило отсутствует
                    if (!$sql->num()) {
                        return ['s' => 'ok'];
                    }
                } else {
                    $sql->query('SELECT `id`, `sip` FROM `firewall` WHERE `sip`="' . $source . '" AND `server`="' . $id . '" LIMIT 1');

                    // Если одиночный адрес не найден, проверить на блокировку подсети
                    if (!$sql->num()) {
                        $source = System::whois($source);

                        $sql->query('SELECT `id` FROM `firewall` WHERE `sip`="' . $source . '" AND `server`="' . $id . '" LIMIT 1');

                        if ($sql->num()) {
                            $firewall = $sql->get();

                            return ['i' => 'Указанный адрес входит в заблокированную подсеть, разблокировать подсеть?', 'id' => $firewall['id']];
                        }

                        return ['s' => 'ok'];
                    }
                }

                $firewall = $sql->get();

                $ssh->set('iptables -D INPUT -s ' . $firewall['sip'] . ' -p udp -d ' . $ip . ' --dport ' . $port . ' -j DROP;'
                    . 'sed "`nl ' . $cfg['iptables'] . ' | grep \"#' . $firewall['id'] . '\" | awk \'{print $1","$1+1}\'`d" ' . $cfg['iptables'] . ' > ' . $cfg['iptables'] . '_temp; cat ' . $cfg['iptables'] . '_temp > ' . $cfg['iptables'] . '; rm ' . $cfg['iptables'] . '_temp');

                $sql->query('DELETE FROM `firewall` WHERE `id`="' . $firewall['id'] . '" LIMIT 1');

                return ['s' => 'ok'];

            case 'remove':
                $sql->query('SELECT `id`, `sip`, `dest` FROM `firewall` WHERE `server`="' . $id . '"');

                $aRule = [];

                while ($firewall = $sql->get()) {
                    [$ip, $port] = explode(':', $firewall['dest']);

                    $aRule[$firewall['id']] = 'iptables -D INPUT -s ' . $firewall['sip'] . ' -p udp -d ' . $ip . ' --dport ' . $port . ' -j DROP;';
                }

                $nRule = count($aRule);

                if (!$nRule) {
                    return null;
                }

                $cmd = '';

                foreach ($aRule as $line => $rule) {
                    $cmd .= $rule . 'sed "`nl ' . $cfg['iptables'] . ' | grep "#' . $line . '" | awk \'{print $1","$1+1}\'`d" ' . $cfg['iptables'] . ' > ' . $cfg['iptables'] . '_temp; cat ' . $cfg['iptables'] . '_temp > ' . $cfg['iptables'] . '; rm ' . $cfg['iptables'] . '_temp';
                }

                $ssh->set($cmd);

                $sql->query('DELETE FROM `firewall` WHERE `server`="' . $id . '" LIMIT ' . $nRule);

                return ['s' => 'ok'];
        }
    }
}
