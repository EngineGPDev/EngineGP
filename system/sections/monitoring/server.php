<?php
if(!DEFINED('EGP')){
    header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404');
    exit();
}

// Получаем ID сервера
$id = intval($url['id']);

// Если ID пустой
if(empty($id)){
	header('Refresh: 0; URL='.$cfg['http'].'monitoring');
    exit();
}

// Meta Title страницы
$title = 'Мониторинг  | Сервер #'.$id;

// Навигация
$html->nav('Мониторинг', $cfg['http'].'monitoring');
$html->nav('Сервер #'.$id);

// Получаем информацию о сервере
$sql->query("SELECT `id`, `unit`, `tarif`, `address`, `name`, `map`, `slots_start`, `online`, `players`, `status`, `game`, `pack`, `date` FROM servers WHERE id = '{$id}' LIMIT 1");
$server = $sql->get();

// Если результат пустой
if(empty($server)){
	header('Refresh: 0; URL='.$cfg['http'].'monitoring');
    exit();
}

// Получаем название локации
$sql->query("SELECT name FROM units WHERE id = '{$server['unit']}' LIMIT 1");
$unit = $sql->get();

// Получаем название тарифа и доступные сборки
$sql->query("SELECT name, packs FROM tarifs WHERE id = '{$server['tarif']}' LIMIT 1");
$tarif = $sql->get();

// Получаем массив сборок
$aPacks = json_decode(base64_decode($tarif['packs']), true);

// Получаем ключ для графиков
$sql->query("SELECT `key` FROM graph WHERE server = '{$id}' LIMIT 1");

// Если ключ отсуствует, создаем
if(!$sql->num()){

    // Генерируем ключ
    $key = md5($id.sys::key('graph'));

    // Добавляем в DB
    $sql->query("INSERT INTO graph SET `server` = '{id}', `key` = '{key}', `time` = '0'");
}else{

    // Получаем ключ из бд
    $graph = $sql->get();
    $key = $graph['key'];
}

// Подготовка страницы
$html->get('server', 'sections/monitoring');
    $html->set('id', $server['id']);
    $html->set('key', $key);
    $html->set('address', $server['address']);
    $html->set('name', $server['name']);
    $html->set('map', $server['map']);
    $html->set('slots', $server['slots_start']);
    $html->set('online', $server['online']);
    $html->set('players', base64_decode($server['players']));
    $html->set('unit', $unit['name']);
    $html->set('tarif', $tarif['name']);
    $html->set('img', sys::status($server['status'], $server['game'], $server['map'], 'img'));
    $html->set('pack', $aPacks[$server['pack']]);
    $html->set('create', date("d.m.Y H:m", $server['date']));
$html->pack('main');