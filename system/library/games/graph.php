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

use CpChart\Data;
use CpChart\Image;

class graph
{
    public static function full($server, $slots, $key, $time)
    {
        // Массивы данных
        $aData = graph::graph_data($time, $server, $slots);

        $aOnline = $aData['online'];

        $aCPU = $aData['cpu'];
        $aRAM = $aData['ram'];
        $aHDD = $aData['hdd'];

        $MyData = new Data();

        // Онлайн
        $MyData->addPoints($aOnline, 'ONLINE');

        // CPU
        $MyData->addPoints($aCPU, 'CPU');

        // RAM
        $MyData->addPoints($aRAM, 'RAM');

        // HDD
        $MyData->addPoints($aHDD, 'HDD');

        // Время
        $MyData->addPoints($aData['time'], 'TIME');

        $MyData->setSerieOnAxis('CPU', 1);
        $MyData->setSerieOnAxis('RAM', 1);
        $MyData->setSerieOnAxis('HDD', 1);
        $MyData->setAxisPosition(1, AXIS_POSITION_RIGHT);

        $MyData->setAxisName(0, 'Онлайн');
        $MyData->setAxisName(1, 'Нагрузка %');

        $MyData->setAbscissa('TIME');

        // Сечение линии
        $MyData->setSerieTicks('ONLINE', 4);

        // Цвет линий
        $MyData->setPalette('ONLINE', ['R' => 68, 'G' => 148, 'B' => 224]);
        $MyData->setPalette('CPU', ['R' => 216, 'G' => 65, 'B' => 65]);
        $MyData->setPalette('RAM', ['R' => 26, 'G' => 150, 'B' => 38]);
        $MyData->setPalette('HDD', ['R' => 205, 'G' => 196, 'B' => 37]);

        $myPicture = new Image(896, 220, $MyData);

        $myPicture->drawFilledRectangle(0, 0, 896, 220, ['R' => 255, 'G' => 255, 'B' => 255]);

        $myPicture->drawRectangle(0, 0, 895, 219, ['R' => 221, 'G' => 221, 'B' => 221]);

        $myPicture->setFontProperties(['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]);
        $myPicture->setGraphArea(40, 20, 616, 190);
        $myPicture->drawFilledRectangle(40, 20, 616, 190, ['R' => 240, 'G' => 242, 'B' => 242, 'Alpha' => 100]);
        $myPicture->drawScale(['XMargin' => 5, 'YMargin' => 5, 'GridR' => 76, 'GridG' => 109, 'GridB' => 120, 'LabelSkip' => 0, 'DrawSubTicks' => true, 'Mode' => SCALE_MODE_MANUAL, 'ManualScale' => [0 => ['Min' => 0, 'Max' => 32], 1 => ['Min' => 0, 'Max' => 100]]]);

        $myPicture->drawText(676, 34, 'Средний онлайн: ' . graph::average($aOnline), ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);
        $myPicture->drawText(676, 54, 'Средняя нагрузка (CPU): ' . graph::average($aCPU) . '%', ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);
        $myPicture->drawText(676, 74, 'Средняя нагрузка (RAM): ' . graph::average($aRAM) . '%', ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);
        $myPicture->drawText(676, 94, 'Средняя нагрузка (HDD): ' . graph::average($aHDD), ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);

        $myPicture->drawText(676, 129, 'Максимальный онлайн: ' . max($aOnline), ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);
        $myPicture->drawText(676, 153, 'Максимальная нагрузка (CPU): ' . max($aCPU) . '%', ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);
        $myPicture->drawText(676, 173, 'Максимальная нагрузка (RAM): ' . max($aRAM) . '%', ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);
        $myPicture->drawText(676, 193, 'Максимальная нагрузка (HDD): ' . max($aHDD), ['R' => 25, 'G' => 25, 'B' => 25, 'FontName' => LIB . 'games/graph/fonts/arianamu.ttf', 'FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMLEFT]);

        $myPicture->setFontProperties(['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 7]);
        $myPicture->drawSplineChart();
        $myPicture->setShadow(false);

        $myPicture->drawLegend(466, 10, ['Style' => LEGEND_NOBORDER, 'Mode' => LEGEND_HORIZONTAL]);

        $myPicture->render(TEMP . (md5($key . 'full_' . $time)) . '.png');

        unset($MyData, $myPicture);

        return null;
    }

    public static function first($server, $aPoints, $aStyle, $style, $key)
    {
        global $cfg, $aGname;

        $MyData = new Data();

        $server_address = $server['address'] . ':' . $server['port'];

        // Значения
        $MyData->addPoints($aPoints, 'ONLINE');

        $MyData->addPoints([VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID], 'NONE');
        $MyData->setAbscissa('NONE');

        // Цвет линии
        $MyData->setPalette('ONLINE', $aStyle[$style]['line']);

        // Размер баннера
        $myPicture = new Image(160, 248, $MyData);

        // Цвет фона
        $myPicture->drawFilledRectangle(0, 0, 160, 248, $aStyle[$style]['fon']);

        // Обводка
        $myPicture->drawRectangle(0, 0, 159, 247, $aStyle[$style]['border']);

        // Шрифт текста
        $myPicture->setFontProperties(array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 5]));

        // Размер графика
        $myPicture->setGraphArea(35, 160, 150, 210);

        // Цвет фона графика
        $myPicture->drawFilledRectangle(35, 160, 150, 210, $aStyle[$style]['graph']);

        // График
        $myPicture->drawScale(['XMargin' => 5, 'YMargin' => 5, 'CycleBackground' => true, 'LabelSkip' => 0, 'DrawSubTicks' => true, 'Mode' => SCALE_MODE_MANUAL, 'Factors' => [$server['slots_start']], 'ManualScale' => [0 => ['Min' => 0, 'Max' => $server['slots_start']]]]);

        // Название игрового сервера
        $myPicture->drawFilledRectangle(0, 0, 18, 248, $aStyle[$style]['leftbox']);
        $myPicture->drawText(14, 245, $server['name'], array_merge($aStyle[$style]['boxcolor'], ['Angle' => 90, 'FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 10]));
        $myPicture->drawFilledRectangle(0, 0, 18, 5, $aStyle[$style]['leftbox']);

        // Адрес игрового сервера
        $myPicture->drawFilledRectangle(25, 5, 153, 18, $aStyle[$style]['box']);
        $myPicture->drawText(28, 17, 'Адрес сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(26, 30, $server_address, array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Статус игрового сервера
        $myPicture->drawFilledRectangle(25, 35, 153, 48, $aStyle[$style]['box']);
        $myPicture->drawText(28, 47, 'Состояние сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(26, 62, graph::status($server['status'], $server['map']), array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Игроки на игровом сервере
        $myPicture->drawFilledRectangle(25, 65, 153, 78, $aStyle[$style]['box']);
        $myPicture->drawText(28, 77, 'Игроки на сервере', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(26, 92, $server['online'] . ' / ' . $server['slots_start'], array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Полоска загрузки
        $myPicture->drawProgress(83, 82, ceil($server['slots_start'] / 100 * $server['online']), array_merge($aStyle[$style]['progress'], ['Width' => 70, 'Height' => 8]));

        // Тип игрового сервера
        $myPicture->drawFilledRectangle(25, 95, 153, 108, $aStyle[$style]['box']);
        $myPicture->drawText(28, 107, 'Тип сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(26, 122, $aGname[$server['game']], array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Хостинг игровых серверов
        $myPicture->drawText(89, 230, $cfg['graph'], array_merge($aStyle[$style]['color'], ['FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMMIDDLE]));
        $myPicture->drawText(89, 245, 'Хостинг игровых серверов', array_merge($aStyle[$style]['color'], ['FontSize' => 8, 'Align' => TEXT_ALIGN_BOTTOMMIDDLE]));

        $myPicture->drawSplineChart();

        $myPicture->render(TEMP . (md5($key . $style . 'first')) . '.png');

        unset($MyData, $myPicture);

        return null;
    }

    public static function second($server, $aPoints, $aStyle, $style, $key)
    {
        global $cfg, $aGname;

        $MyData = new Data();

        $server_address = $server['address'] . ':' . $server['port'];

        // Значения
        $MyData->addPoints($aPoints, 'ONLINE');

        $MyData->addPoints([VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID, VOID], 'NONE');
        $MyData->setAbscissa('NONE');

        // Цвет линии
        $MyData->setPalette('ONLINE', $aStyle[$style]['line']);

        // Размер баннера
        $myPicture = new Image(560, 95, $MyData);

        // Цвет фона
        $myPicture->drawFilledRectangle(0, 0, 560, 95, $aStyle[$style]['fon']);

        // Название игрового сервера
        $myPicture->drawFilledRectangle(5, 5, 410, 18, $aStyle[$style]['box']);
        $myPicture->drawText(8, 17, 'Название сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(6, 31, $server['name'], array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Костыль для "обрезания" названия сервера
        $myPicture->drawFilledRectangle(405, 19, 560, 35, $aStyle[$style]['fon']);

        // Обводка
        $myPicture->drawRectangle(0, 0, 559, 94, $aStyle[$style]['border']);

        // Шрифт текста
        $myPicture->setFontProperties(array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 5]));

        // Размер графика
        $myPicture->setGraphArea(430, 5, 554, 60);

        // Цвет фона графика
        $myPicture->drawFilledRectangle(430, 5, 554, 60, $aStyle[$style]['graph']);

        // График
        $myPicture->drawScale(['XMargin' => 5, 'YMargin' => 5, 'CycleBackground' => true, 'LabelSkip' => 0, 'DrawSubTicks' => true, 'Mode' => SCALE_MODE_MANUAL, 'Factors' => [$server['slots_start']], 'ManualScale' => [0 => ['Min' => 0, 'Max' => $server['slots_start']]]]);

        // Адрес игрового сервера
        $myPicture->drawFilledRectangle(5, 36, 210, 49, $aStyle[$style]['box']);
        $myPicture->drawText(8, 48, 'Адрес сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(6, 62, $server_address, array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Статус игрового сервера
        $myPicture->drawFilledRectangle(215, 36, 410, 49, $aStyle[$style]['box']);
        $myPicture->drawText(218, 48, 'Состояние сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(216, 62, graph::status($server['status'], $server['map']), array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Игроки на игровом сервере
        $myPicture->drawFilledRectangle(5, 67, 210, 80, $aStyle[$style]['box']);
        $myPicture->drawText(8, 79, 'Игроки на сервере', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(6, 93, $server['online'] . ' / ' . $server['slots_start'], array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Полоска загрузки
        $myPicture->drawProgress(90, 83, ceil($server['slots_start'] / 100 * $server['online']), array_merge($aStyle[$style]['progress'], ['Width' => 120, 'Height' => 8]));

        // Тип игрового сервера
        $myPicture->drawFilledRectangle(215, 67, 410, 80, $aStyle[$style]['box']);
        $myPicture->drawText(218, 79, 'Тип сервера', array_merge($aStyle[$style]['boxcolor'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));
        $myPicture->drawText(216, 91, $aGname[$server['game']], array_merge($aStyle[$style]['color'], ['FontName' => LIB . 'games/graph/fonts/tahoma.ttf', 'FontSize' => 8]));

        // Хостинг игровых серверов
        $myPicture->drawText(490, 77, $cfg['graph'], ['FontSize' => 10, 'Align' => TEXT_ALIGN_BOTTOMMIDDLE]);
        $myPicture->drawText(490, 90, 'Хостинг игровых серверов', ['FontSize' => 8, 'Align' => TEXT_ALIGN_BOTTOMMIDDLE]);

        $myPicture->drawSplineChart();

        $myPicture->render(TEMP . (md5($key . $style . 'second')) . '.png');

        unset($MyData, $myPicture);

        return null;
    }

    public static function online_day($server, $max)
    {
        global $sql, $start_point;

        $time = $start_point - 86400;

        $aOnline = [];

        $aSel = [];

        $sql->query('SELECT `online` FROM `graph_hour` WHERE `server`="' . $server . '" AND `time`>"' . $time . '" ORDER BY `id` DESC LIMIT 24');
        while ($value = $sql->get()) {
            $aSel[] = $value['online'];
        }

        $n = count($aSel);

        $n = $n > 1 ? $n - 1 : 0;

        if ($n) {
            for ($i = $n; $i >= 0; $i -= 1) {
                $aOnline[] = $aSel[$i] > $max ? $max : $aSel[$i];
            }
        }

        $add = count($aOnline);

        if ($add < 24) {
            for ($i = $n; $i <= 23; $i += 1) {
                $aOnline[] = 0;
            }
        }

        return $aOnline;
    }

    private static function graph_data($period, $server, $max)
    {
        global $sql, $start_point;

        $aData = [
            'limit' => [
                'day' => 24,
                'week' => 7,
                'month' => 30,
            ],

            'for' => [
                'day' => 23,
                'week' => 6,
                'month' => 29,
            ],

            'from' => [
                'day' => 'graph_hour',
                'week' => 'graph_day',
                'month' => 'graph_day',
            ],

            'time' => [
                'day' => 86400,
                'week' => 604800,
                'month' => 2592000,
            ],
        ];

        $time = $start_point - $aData['time'][$period];

        $aOnline = [];

        $aCPU = [];
        $aRAM = [];
        $aHDD = [];
        $aTime = [];

        $aSel = [];

        $sql->query('SELECT `online`, `cpu`, `ram`, `hdd`, `time` FROM `' . $aData['from'][$period] . '` WHERE `server`="' . $server . '" AND `time`>"' . $time . '" ORDER BY `id` DESC LIMIT ' . $aData['limit'][$period]);
        while ($value = $sql->get()) {
            $aSel[] = ['online' => $value['online'], 'cpu' => $value['cpu'], 'ram' => $value['ram'], 'hdd' => $value['hdd'], 'time' => $value['time']];
        }

        $n = count($aSel);

        $n = $n > 1 ? $n - 1 : 0;

        $next = true;

        if (isset($aSel[$n]['online'])) {
            for ($i = $n; $i >= 0; $i -= 1) {
                $aOnline[] = $aSel[$i]['online'] > $max ? $max : $aSel[$i]['online'];

                $aCPU[] = $aSel[$i]['cpu'];
                $aRAM[] = $aSel[$i]['ram'];
                $aHDD[] = $aSel[$i]['hdd'];

                if ($next) {
                    $aTime[] = VOID;

                    $next = false;
                } else {
                    $aTime[] = $period == 'day' ? date('H:i', $aSel[$i]['time']) : date('d.m', $aSel[$i]['time']);

                    $next = true;
                }
            }
        }

        $add = count($aOnline);

        for ($i = $add; $i <= $aData['for'][$period]; $i += 1) {
            $aOnline[] = 0;

            $aCPU[] = 0;
            $aRAM[] = 0;
            $aHDD[] = 0;

            $aTime[] = VOID;
        }

        return ['online' => $aOnline, 'cpu' => $aCPU, 'ram' => $aRAM, 'hdd' => $aHDD, 'time' => $aTime];
    }

    private static function status($status, $map)
    {
        switch ($status) {
            case 'working':
                return 'Карта: ' . $map;
            case 'off':
                return 'Статус: выключен';
            case 'start':
                return 'Статус: запускается';
            case 'restart':
                return 'Статус: перезапускается';
            case 'change':
                return 'Статус: меняется карта';
            case 'install':
                return 'Статус: устанавливается';
            case 'reinstall':
                return 'Статус: переустанавливается';
            case 'update':
                return 'Статус: обновляется';
            case 'recovery':
                return 'Статус: восстанавливается';
            case 'overdue':
                return 'Статус: просрочен';
            case 'blocked':
                return 'Статус: заблокирован';
        }
    }

    private static function average($arr)
    {
        return !count($arr) ? 0 : ceil(array_sum($arr) / count($arr));
    }
}
