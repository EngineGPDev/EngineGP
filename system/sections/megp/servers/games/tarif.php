<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

include(LIB . 'games/games.php');
include(LIB . 'games/tarifs.php');
include(LIB . 'games/' . $server['game'] . '/tarif.php');

// Выполнение операции
if (isset($url['subsection']) and in_array($url['subsection'], $aSub)) {
    $nmch = sys::rep_act('server_tarif_go_' . $id, 10);

    if (file_exists(SEC . 'megp/servers/' . $server['game'] . '/tarif/' . $url['subsection'] . '.php'))
        include(SEC . 'megp/servers/' . $server['game'] . '/tarif/' . $url['subsection'] . '.php');
    else
        include(SEC . 'megp/servers/games/tarif/' . $url['subsection'] . '.php');
}

// Общий шаблон раздела
$html->get('tarif', 'sections/servers/games');

$html->set('id', $id);

$html->pack('main');

// Шаблон продления
if ($cfg['settlement_period'])
    tarif::extend_sp($server, $tarif, $id);
else {
    $options = games::parse_time(explode(':', $tarif['timext']), $tarif['discount'], $server['tarif'], 'extend');

    tarif::extend($options, $server, $tarif['name'], $id);
}

// Если не тестовый период
if (!$server['test']) {
    $sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    // Шаблон смены тарифа (если аренда не менее 1 дня и цены планов различны)
    if ($server['time'] > $start_point + 86400 and tarif::price($tarif['price']))
        tarif::plan($server, $tarif['name'], $id);

    // Шаблон изменения кол-ва слот
    if ($tarif['slots_min'] != $tarif['slots_max'])
        tarif::slots($server, array('min' => $tarif['slots_min'], 'max' => $tarif['slots_max']), $id);
}
?>