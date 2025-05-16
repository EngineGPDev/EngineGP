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

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

class sys
{
    public static function url($all = true)
    {
        if ($_SERVER['REQUEST_URI'] == '/acp/') {
            return $all ? null : 'index';
        }

        $url = [];

        $string = str_replace('//', '/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $aUrl = explode('/', trim($string, ' /'));

        array_shift($aUrl);

        if (!$all) {
            return $aUrl[0];
        }

        unset($aUrl[0]);

        $i = 1;
        $m = count($aUrl) + 1;

        for ($i; $i < $m; $i += 1) {
            $url[$aUrl[$i]] = isset($aUrl[++$i]) ? $aUrl[$i] : true;
        }

        return $url;
    }

    public static function int($data, $width = false)
    {
        if ($width) {
            return preg_replace("([^0-9]{0, " . $width . "})", '', $data);
        }

        return preg_replace("([^0-9])", '', $data);
    }

    public static function first($array = [])
    {
        return $array[0];
    }

    public static function b64js($data)
    {
        return base64_encode(json_encode($data));
    }

    public static function b64djs($data)
    {
        return json_decode(base64_decode($data), true);
    }

    public static function outjs($val, $cache = false)
    {
        global $mcache;

        if ($cache) {
            $mcache->delete($cache);
        }

        die(json_encode($val));
    }

    public static function out($val = '', $cache = false)
    {
        global $mcache;

        if ($cache) {
            $mcache->delete($cache);
        }

        die('' . $val . '');
    }

    public static function valid($val, $type, $preg = '')
    {
        if (!is_string($val)) {
            return true;
        }

        switch ($type) {
            case 'promo':
                if (!preg_match("/^[A-Za-z0-9]{2,20}$/", $val)) {
                    return true;
                }

                return false;

            case 'en':
                if (!preg_match("/^[A-Za-z0-9]$/", $val)) {
                    return true;
                }

                return false;

            case 'ru':
                if (!preg_match("/^[А-Яа-я]$/u", $val)) {
                    return true;
                }

                return false;

            case 'wm':
                if (!preg_match('/^R[0-9]{12,12}$|^Z[0-9]{12,12}$|^U[0-9]{12,12}$/m', $val)) {
                    return true;
                }

                return false;

            case 'ip':
                if (!preg_match("/^(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}$/", $val)) {
                    return true;
                }

                return false;

            case 'steamid':
                if (!preg_match("/^STEAM_[0-9]:[0-9]:[0-9]{6,12}$|^HLTV$|^STEAM_ID_LAN$|^STEAM_ID_PENDING$|^VALVE_ID_LAN$|^VALVE_ID_PENDING$|^STEAM_666:88:666$/", $val)) {
                    return true;
                }

                return false;

            case 'steamid3':
                if (!preg_match("/^\[U:[01]:[0-9]{3,12}\]$/i", $val)) {
                    return true;
                }

                return false;

            case 'num':
                if (!preg_match('/[^0-9]/', $val)) {
                    return true;
                }

                return false;

            case 'md5':
                if (!preg_match("/^[a-z0-9]{32,32}$/", $val)) {
                    return true;
                }

                return false;

            case 'other':
                if (!preg_match($preg, $val)) {
                    return true;
                }

                return false;
        }

        return true;
    }

    public static function page($page, $nums, $num)
    {
        $ceil = ceil($nums / $num);

        if ($page > $ceil) {
            $page = $ceil;
        }

        $next = $page * $num;

        if ($next <= $nums) {
            $next = $next - $num;
        }

        if ($next > $nums) {
            $next = $next - $num;
        }

        if ($next < 1) {
            $next = 0;
        }

        $num_go = $next;
        if ($page == '') {
            $page = 1;
        }

        $aPage = [
            'page' => $page,
            'num' => $num_go,
            'ceil' => $ceil,
        ];

        return $aPage;
    }

    public static function page_list($countnum, $actnum)
    {
        if ($countnum == 0 || $countnum == 1) {
            return [];
        }

        if ($countnum > 10) {
            if ($actnum <= 4 || $actnum + 3 >= $countnum) {
                for ($i = 0; $i <= 4; $i++) {
                    $numlist[$i] = $i + 1;
                }

                $numlist[5] = '...';
                for ($j = 6, $k = 4; $j <= 10; $j += 1, $k -= 1) {
                    $numlist[$j] = $countnum - $k;
                }
            } else {
                $numlist[0] = 1;
                $numlist[1] = 2;
                $numlist[2] = '...';
                $numlist[3] = $actnum - 2;
                $numlist[4] = $actnum - 1;
                $numlist[5] = $actnum;
                $numlist[6] = $actnum + 1;
                $numlist[7] = $actnum + 2;
                $numlist[8] = '...';
                $numlist[9] = $countnum - 1;
                $numlist[10] = $countnum;
            }
        } else {
            for ($n = 0; $n < $countnum; $n += 1) {
                $numlist[$n] = $n + 1;
            }
        }

        return $numlist;
    }

    public static function page_gen($ceil, $page, $actnum, $section)
    {
        global $cfg, $html;

        $aNum = sys::page_list($ceil, $actnum);

        $pages = '';

        $html->get('pages');

        if ($ceil) {
            if ($page != 1) {
                $next = $page - 1;
                $pages .= '<a href="' . $cfg['http'] . $section . '/page/' . $next . '"><i class="fa fa-angle-double-left"></i></a>';
            }

            foreach ($aNum as $v) {
                if ($v != $page && $v != '...') {
                    $pages .= '<a href="' . $cfg['http'] . $section . '/page/' . $v . '">' . $v . '</a>';
                }

                if ($v == $page) {
                    $pages .= '<a href="#" onclick="return false" class="active">' . $v . '</a>';
                }

                if ($v == '...') {
                    $pages .= '<a href="#" onclick="return false">...</a>';
                }
            }

            if ($ceil > $page) {
                if ($page < $ceil) {
                    $next = $page + 1;
                    $pages .= '<a href="' . $cfg['http'] . $section . '/page/' . $next . '"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    $pages .= '<a href="#" onclick="return false;"><i class="fa fa-angle-double-right"></i></a>';
                }
            }
        }

        $html->set('pages', $pages);

        $html->pack('pages');

        return null;
    }

    public static function ago($time, $brackets = false)
    {
        global $start_point;

        $diff = $start_point - $time;

        if ($diff < 0) {
            return '';
        }

        if (!$diff) {
            $diff = 1;
        }

        $seconds = ['секунду', 'секунды', 'секунд'];
        $minutes = ['минуту', 'минуты', 'минут'];
        $hours = ['час', 'часа', 'часов'];
        $days = ['день', 'дня', 'дней'];
        $weeks = ['неделю', 'недели', 'недель'];
        $months = ['месяц', 'месяца', 'месяцев'];
        $years = ['год', 'года', 'лет'];

        $phrase = [$seconds, $minutes, $hours, $days, $weeks, $months, $years];
        $length = [1, 60, 3600, 86400, 604800, 2630880, 31570560];

        for ($i = 6; ($i >= 0) and (($no = $diff / $length[$i]) <= 1); $i -= 1) ;

        if ($i < 0) {
            $i = 0;
        }

        $_time = $start_point - ($diff % $length[$i]);
        $no = ceil($no);

        if ($brackets) {
            return '(' . $no . ' ' . sys::parse_ago($no, $phrase[$i]) . ' назад)';
        }

        return $no . ' ' . sys::parse_ago($no, $phrase[$i]) . ' назад';
    }

    private static function parse_ago($number, $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];

        return $titles[($number % 100 > 4 and $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    public static function date($lenght, $date)
    {
        global $start_point;

        $check_time = $date - $start_point;

        if ($check_time < 1) {
            return 'время истекло.';
        }

        $days = floor($check_time / 86400);
        $hours = floor(($check_time % 86400) / 3600);
        $minutes = floor(($check_time % 3600) / 60);
        $seconds = $check_time % 60;

        $adata = [
            'min' => [
                'days' => ['день', 'дня', 'дней'],
                'hours' => ['ч.', 'ч.', 'ч.'],
                'minutes' => ['мин.', 'мин.', 'мин.'],
                'seconds' => ['сек.', 'сек.', 'сек.'],
            ],
            'max' => [
                'days' => ['день', 'дня', 'дней'],
                'hours' => ['час', 'часа', 'часов'],
                'minutes' => ['минуту', 'минуты', 'минут'],
                'seconds' => ['секунду', 'секунды', 'секунд'],
            ],
        ];

        $text = '';

        if ($days > 0) {
            $text .= sys::date_decl($days, $adata[$lenght]['days']);
        }

        if ($days < 1 and $hours > 0) {
            $text .= ' ' . sys::date_decl($hours, $adata[$lenght]['hours']);
        }

        if ($days < 1 and $minutes > 0) {
            $text .= ' ' . sys::date_decl($minutes, $adata[$lenght]['minutes']);
        }

        if ($days < 1 and $seconds > 0) {
            $text .= ' ' . sys::date_decl($seconds, $adata[$lenght]['seconds']);
        }

        return $text;
    }

    public static function date_decl($digit, $expr, $onlyword = false)
    {
        if (!is_array($expr)) {
            $expr = array_filter(explode(' ', $expr));
        }

        if (empty($expr[2])) {
            $expr[2] = $expr[1];
        }

        $i = sys::int($digit) % 100;

        if ($onlyword) {
            $digit = '';
        }

        if ($i > 4 and $i < 21) {
            $res = $digit . ' ' . $expr[2];
        } else {
            $i %= 10;
        }

        if ($i == 1) {
            $res = $digit . ' ' . $expr[0];
        } elseif ($i > 1 and $i < 5) {
            $res = $digit . ' ' . $expr[1];
        } else {
            $res = $digit . ' ' . $expr[2];
        }

        return trim($res);
    }

    public static function today($time, $cp = false)
    {
        global $start_point;

        $today = date('d.m.Y', $start_point);
        $day = date('d.m.Y', $time);

        if ($day == $today) {
            if ($cp) {
                return 'Сегодня ' . date('H:i', $time);
            }

            return 'Сегодня ' . date('- H:i', $time);
        }

        $yesterday_first = sys::int(sys::first(explode('.', $today))) - 1;
        $yesterday_full = date('m.Y', $time);

        if ($day == $yesterday_first . '.' . $yesterday_full and !$yesterday_first) {
            if ($cp) {
                return 'Вчера ' . date('H:i', $time);
            }

            return 'Вчера ' . date('- H:i', $time);
        }

        if ($cp) {
            return date('d.m.Y H:i', $time);
        }

        return date('d.m.Y - H:i', $time);
    }

    public static function browser($agent)
    {
        if (strpos($agent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        }

        if (strpos($agent, 'Opera') !== false) {
            return 'Opera';
        }

        if (strpos($agent, 'Chrome') !== false) {
            return 'Google Chrome';
        }

        if (strpos($agent, 'MSIE') !== false) {
            return 'Internet Explorer';
        }

        if (strpos($agent, 'Safari') !== false) {
            return 'Safari';
        }

        return 'Неизвестный';
    }

    public static function whois($ip)
    {
        $stack = fsockopen('whois.ripe.net', 43, $errno, $errstr);

        if (!$stack) {
            return 'не определена';
        }

        fputs($stack, $ip . "\r\n");

        $subnetwork = '';

        while (!feof($stack)) {
            $str = fgets($stack, 128);

            if (strpos($str, 'route:') !== false) {
                $subnetwork = trim(str_replace('route:', '', $str));

                break;
            }
        }

        fclose($stack);

        return isset($subnetwork[0]) ? $subnetwork : 'не определена';
    }

    public static function uptime_load($time)
    {
        $uptime = null;

        $day = floor($time / 86400);
        if ($day) {
            $uptime .= $day . 'д. ';
        }

        $hour = floor(($time % 86400) / 3600);
        if ($hour) {
            $uptime .= $hour . 'ч. ';
        }

        $min = floor(($time % 3600) / 60);
        if ($min) {
            $uptime .= $min . 'м. ';
        }

        $sec = $time % 60;
        if ($sec) {
            $uptime .= $sec . 'с. ';
        }

        return $uptime;
    }

    public static function ram_load($data)
    {
        $aData = explode(' ', $data);

        return ceil((floatval($aData[0]) - (floatval($aData[1]) + floatval($aData[2]) + floatval($aData[3]))) * 100 / floatval($aData[0]));
    }

    public static function cpu_load($data)
    {
        $aData = explode(' ', $data);

        $load = ceil(floatval($aData[0]) / floatval($aData[1]));

        return $load > 100 ? 100 : $load;
    }

    public static function checkdate($time)
    {
        $time = explode(' ', $time);

        if (count($time) != 2) {
            sys::outjs(['e' => 'Указанная дата неправильная.']);
        }

        $aDate = explode('/', $time[0]);
        $aTime = explode(':', $time[1]);

        if (!isset($aDate[1], $aDate[0], $aDate[2]) || !checkdate($aDate[1], $aDate[0], $aDate[2])) {
            sys::outjs(['e' => 'Указанная дата неправильная.']);
        }

        return mktime($aTime[0], $aTime[1], 0, $aDate[1], $aDate[0], $aDate[2]);
    }

    public static function passwdkey($passwd)
    {
        return password_hash($passwd, PASSWORD_DEFAULT);
    }

    public static function mail($name, $text, $mail)
    {
        global $cfg;

        $tpl = file_get_contents(DATA . 'mail.ini', "r");

        $text = str_replace(
            ['[name]', '[text]', '[http]', '[img]', '[css]'],
            [$cfg['name'], $text, $cfg['http'], $cfg['http'] . 'template/images/', $cfg['http'] . 'template/css/'],
            $tpl
        );

        $dsn = $_ENV['MAILER_DSN'];
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from($_ENV['MAIL_FROM_NAME'] . ' <' . $_ENV['MAIL_FROM_ADDRESS'] . '>')
            ->to($mail)
            ->subject($name)
            ->html($text);

        try {
            $mailer->send($email);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function country($address)
    {
        global $SxGeo;

        if (sys::valid($address, 'ip')) {
            return 'не определена';
        }

        $data = $SxGeo->getCityFull($address);

        if ($data === false || !isset($data['country']['name_ru'])) {
            return 'не определена';
        }

        return $data['country']['name_ru'] != '' ? $data['country']['name_ru'] : 'не определена';
    }

    public static function ipproxy()
    {
        global $_SERVER;

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return null;
    }

    public static function ip()
    {
        $ip = sys::ipproxy();

        if (sys::valid($ip, 'ip')) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public static function status($data)
    {
        if (strpos($data, 'is running') || strpos($data, '(running)')) {
            return true;
        }

        return false;
    }

    public static function strlen($str)
    {
        return iconv_strlen($str, 'UTF-8');
    }

    public static function bbc($text)
    {
        global $cfg;

        $lines = explode("\n", $text);

        $str_search = [
            "#\[spoiler\](.+?)\[\/spoiler\]#is",
            "#\[sp\](.+?)\[\/sp\]#is",
            "#\[b\](.+?)\[\/b\]#is",
            "#\[u\](.+?)\[\/u\]#is",
            "#\[code\](.+?)\[\/code\]#is",
            "#<code>(.+?)<\/code>#isU",
            "#\[quote\](.+?)\[\/quote\]#is",
            "#\[url=(.+?)\](.+?)\[\/url\]#is",
            "#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is",
        ];

        $str_replace = [
            "<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'>\\1</div></div>",
            "<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'>\\1</div></div>",
            "<b>\\1</b>",
            "<u>\\1</u>",
            "<div><b class='spoiler'>Посмотреть содержимое</b><div class='spoiler_main'><pre><code>\\1</code></pre></div></div>",
            function ($matches) {
                return '<code>' . htmlspecialchars($matches[1]) . '</code>';
            },
            "<blockquote><p>\\1</p></blockquote>",
            "<a href='\\1'>\\2</a>",
            "<a href='\\2'>\\2</a>",
        ];

        $uptext = '';

        foreach ($lines as $line) {
            foreach ($str_search as $key => $pattern) {
                if (is_callable($str_replace[$key])) {
                    $line = preg_replace_callback($pattern, $str_replace[$key], $line);
                } else {
                    $line = preg_replace($pattern, $str_replace[$key], $line);
                }
            }
            $uptext .= $line . PHP_EOL;
        }

        return $uptext;
    }

    public static function text($section, $name)
    {
        global $cfg, $user;

        $group = $user['group'] ?? 'user';

        if ($section != 'error' || !$cfg['text_group']) {
            $group = 'all';
        }

        include(DATA . 'text/' . $section . '.php');

        return $text[$name][$group] ?? $text[$name];
    }

    public static function updtext($text, $data)
    {
        foreach ($data as $name => $val) {
            $text = str_replace('[' . $name . ']', $val, $text);
        }

        return $text;
    }

    public static function logMessage($message, $logFile = 'enginegp_info', $context = [])
    {
        $logger = new Logger('EngineGP');
        $logger->pushHandler(new StreamHandler(ROOT . '/logs/' . $logFile . '.log'));
        $logger->info($message, $context);
    }
}
