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

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\PlainTextHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Подключение filp/whoops
$whoops = new Run();
$prettyPageHandler = new PrettyPageHandler();
foreach ($cfg['whoops']['blacklist'] as $key => $secrets) {
    foreach ($secrets as $secret) {
        $prettyPageHandler->blacklist($key, $secret);
    }
}
$whoops->pushHandler($prettyPageHandler);
// логи в файл
$loggingInFile = new PlainTextHandler();
$loggingInFile->loggerOnly(true);
$loggingInFile->setLogger((new Logger('EngineGP', [(new StreamHandler(ROOT . '/logs/cron.log'))->setFormatter((new LineFormatter(null, null, true)))])));
$whoops->pushHandler($loggingInFile);
$whoops->register();

// Подгрузка трейта
if (!file_exists(CRON . $task . '.php')) {
    exit('Invalid cron method');
}

$user = ['id' => 0, 'group' => 'admin'];

class cron
{
    public static $seping = 5;

    public static $process = [
        'cs' => 'hlds_',
        'cssold' => 'srcds_i686',
        'css' => 'srcds_',
        'csgo' => 'srcds_',
        'cs2' => 'cs2',
        'rust' => 'Rust',
        'samp' => 'samp',
        'crmp' => 'samp',
        'mta' => 'mta',
        'mc' => 'java',
    ];

    public static $quakestat = [
        'cs' => 'a2s',
        'cssold' => 'a2s',
        'css' => 'a2s',
        'csgo' => 'a2s',
        'cs2' => 'a2s',
        'mta' => 'eye',
    ];

    public static $admins_file = [
        'cs' => 'cstrike/addons/amxmodx/configs/users.ini',
        'cssold' => 'cstrike/addons/sourcemod/configs/admins_simple.ini',
        'css' => 'cstrike/addons/sourcemod/configs/admins_simple.ini',
        'csgo' => 'csgo/addons/sourcemod/configs/admins_simple.ini',
        'cs2' => 'csgo/addons/sourcemod/configs/admins_simple.ini',
    ];

    public static function thread($num, $type, $aData)
    {
        $threads = [];

        for ($n = 1; $n <= $num; $n += 1) {
            $data = '';

            $i = 0;

            foreach ($aData as $key => $val) {
                if ($i == cron::$seping) {
                    break;
                }

                $data .= $val . ' ';

                unset($aData[$key]);

                $i += 1;
            }

            $aData = array_values($aData);

            $threads[] = $type . ' ' . substr($data, 0, -1);
        }

        return $threads;
    }
}

include(CRON . $task . '.php');

new $task();
