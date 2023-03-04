<?php
if(!DEFINED('EGP')){
    header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404');
    exit();
}

// Meta Title страницы
$title = 'Мониторинг игровых серверов';

// Навигация
$html->nav('Мониторинг');

// Дополнительная переменная
$type = false;
$i = 0;

// Получаем значение пагинации
if(isset($url['page'])){
    $page = sys::clean($url['page'], "int");
}else{
    $page = 1;
}

// Проверяем задано ли у нас фильтрация по типу игры
if(isset($url['game']) AND in_array($url['game'], array('cs', 'css', 'cssold', 'csgo', 'samp', 'crmp', 'mta', 'mc'))){
    $type = $url['game'];
}

// Если идет сортировка по игре
if($type){

    // SQL запрос для выборки
    $qSql = "game = '{$type}' AND status = 'working'";

    // Задаем переменной колличество серверов всего, результат кэша
    $all = $mcache->get('monitoring_list_count_'.$type);

    // Если кэш пуст
    if(!$all){

        // Получаем инфу из бд, кооличество серверов всего
        $sql->query("SELECT id FROM servers WHERE {$qSql}");
        $all = $sql->num();

        // Закидываем значеие в кэш на 2 минуты
        $mcache->set('monitoring_list_count_'.$type, $all, false, 120);
    }

    // Массив для построения страниц
    $aPage = sys::page($page, $all, 30);

    // Генерация массива ($html->arr['pages']) страниц
    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'monitoring/type/'.$type);
}else{

    // SQL запрос для выборки
    $qSql = "status = 'working'";

    // Задаем переменной колличество серверов всего, результат кэша
    $all = $mcache->get('monitoring_list_count');

    // Если кэш пуст
    if(!$all){

        // Получаем инфу из бд, кооличество серверов всего
        $sql->query("SELECT id FROM servers WHERE {$qSql}");
        $all = $sql->num();

        // Закидываем значеие в кэш на 2 минуты
        $mcache->set('monitoring_list_count', $all, false, 120);
    }

    // Массив для построения страниц
    $aPage = sys::page($page, $all, 30);

    // Генерация массива ($html->arr['pages']) страниц
    sys::page_gen($aPage['ceil'], $page, $aPage['page'], 'monitoring');
}


// Получаем список серверов
$sql->query("SELECT `id`, `address`, `name`, `map`, `slots_start`, `online` FROM servers WHERE {$qSql} ORDER BY `id` ASC LIMIT {$aPage['num']}, 30");

// Циклически собираем шаблон серверов
while($server = $sql->get()) {
    
    // Увеличиваем значение ID
    $i+=1;

    // Собираем шаблон
    $html->get('list', 'sections/monitoring');
        $html->set('id', $i);
        $html->set('server', $server['id']);
        $html->set('address', $server['address']);
        $html->set('name', $server['name']);
        $html->set('map', $server['map']);
        $html->set('slots', $server['slots_start']);
        $html->set('online', $server['online']);
    $html->pack('monitoring_list');
}

$games = array('cs', 'cssold', 'css', 'csgo', 'samp', 'crmp', 'mta', 'mc');
    $online = array();
    foreach($games as $game)
    {
        $sql->query('SELECT SUM(`online`) AS `online` FROM `servers` WHERE (`status`="working" OR `status`="change") AND `game`="'.$game.'"');
        $online[$game] = $sql->get()['online'];
    }

// Подготовка страницы
$html->get('all', 'sections/monitoring');
foreach($games as $game)
        if(!empty($online[$game]))
            $html->set('o_'.$game, $online[$game]);
        else
            $html->set('o_'.$game, '0');
    $html->set('list', isset($html->arr['monitoring_list']) ? $html->arr['monitoring_list'] : '');
    $html->set('pages', isset($html->arr['pages']) ? $html->arr['pages'] : '');
$html->pack('main');