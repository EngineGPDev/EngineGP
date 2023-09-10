<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT `unit`, `tarif` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$html->nav($server['address'], $cfg['http'] . 'servers/id/' . $id);

// Подразделы
$aSub = ['install', 'delete', 'list', 'listing', 'search'];

// Если выбран подраздел
if (isset($url['subsection']) and in_array($url['subsection'], $aSub)) {
    $html->nav('Карты', $cfg['http'] . 'servers/id/' . $id . '/section/maps');

    $nmch = sys::rep_act('server_maps_go_' . $id, 10);

    require(SEC . 'servers/' . $server['game'] . '/maps/' . $url['subsection'] . '.php');
} else {
    $html->nav('Карты');

    // Построение списка установленных карт
    $sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
    $unit = $sql->get();

    if (!isset($ssh))
        require(LIB . 'ssh.php');

    if (!$ssh->auth($unit['passwd'], $unit['address'])) {
        if ($go)
            sys::outjs(['e' => sys::text('error', 'ssh')], $nmch);

        sys::back($cfg['http'] . 'servers/id/' . $id);
    }

    $sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
    $tarif = $sql->get();

    $ssh->set('cd ' . $tarif['install'] . $server['uid'] . '/csgo/maps/ && du -ah | grep -e "\.bsp$" | awk \'{print $2}\'');

    $maps = $ssh->get();

    $aMaps = explode("\n", str_ireplace('.bsp', '', (string) $maps));

    // Сортировка карт
    sort($aMaps);
    reset($aMaps);

    $mapsjs = '';
    $i = 0;

    foreach ($aMaps as $index => $map) {
        if (!isset($map[3]))
            continue;

        $map = str_replace('./', '', $map);

        $mapjs = str_replace('$', '-_-', $map);

        $aName = explode('/', $map);
        $name = end($aName);

        $html->get('map_server', 'sections/servers/csgo/maps');
        $html->set('img', sys::img($name, $server['game']));
        $html->set('map', $mapjs);
        $html->set('name', $name);

        if (count($aName) > 1)
            $html->unit('workshop', true, true);
        else {
            $i += 1;
            $mapsjs .= $i . ' : "' . $mapjs . '",';

            $html->unit('workshop', false, true);
        }
        $html->pack('maps');
    }

    // Если есть кеш
    if ($mcache->get('server_maps_' . $id) != '')
        $html->arr['main'] = $mcache->get('server_maps_' . $id);
    else {
        $html->get('maps', 'sections/servers/games');
        $html->set('id', $id);
        $html->set('types', $html->arr['types'] ?? '');
        $html->set('maps', $html->arr['maps'] ?? '');
        $html->set('mapsjs', $mapsjs);
        $html->pack('main');

        $mcache->set('server_maps_' . $id, $html->arr['main'], false, 3);
    }
}
