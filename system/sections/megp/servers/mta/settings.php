<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$sql->query('SELECT `uid`, `unit`, `tarif`, `pack`, `ddos` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$aSub = array('start', 'pack');

// Если выбран подраздел
if (isset($url['subsection']) and in_array($url['subsection'], $aSub)) {
    if ($go)
        $nmch = sys::rep_act('server_settings_go_' . $id, 10);

    $dir = $url['subsection'] == 'start' ? 'megp/' : '';

    if (in_array($url['subsection'], $aRouteSub['settings']))
        include(SEC . 'servers/games/settings/' . $url['subsection'] . '.php');
    else
        include(SEC . $dir . 'servers/' . $server['game'] . '/settings/' . $url['subsection'] . '.php');
} else {
    if ($mcache->get('server_settings_' . $id) != '')
        $html->arr['main'] = $mcache->get('server_settings_' . $id);
    else {
        $sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
        $tarif = $sql->get();

        // Построение списка доступных сборок
        $aPacks = sys::b64djs($tarif['packs']);

        $packs = '<option value="' . $server['pack'] . '">' . $aPacks[$server['pack']] . '</option>';
        unset($aPacks[$server['pack']]);

        foreach ($aPacks as $pack => $desc)
            $packs .= '<option value="' . $pack . '">' . $desc . '</option>';

        $html->get('settings', 'sections/servers/' . $server['game']);
        $html->set('id', $id);
        $html->set('packs', $packs);
        $html->pack('main');

        $mcache->set('server_settings_' . $id, $html->arr['main'], false, 20);
    }
}
?>