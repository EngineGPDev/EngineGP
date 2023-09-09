<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

require(DATA . 'boost.php');

$info = '<i class="fa fa-cloud"></i> Статистика BOOST CS: 1.6';

$aSection = $aBoost['cs']['boost'];

if ($section == 'search')
    require(SEC . 'boost/search.php');

if (!in_array($section, $aSection))
    $section = 'index';

$html->get('menu', 'sections/boost');

$boosts = '';

if ($section != 'index')
    $html->unit('s_index');
else
    $html->unit('s_index', true);

foreach ($aSection as $service) {
    if ($section == $service)
        $boosts .= '<li><a href="[acp]boost/section/' . $section . '" class="active"><i class="fa fa-list-ol"></i> ' . $aBoost['cs'][$section]['site'] . '</a></li>';
    else
        $boosts .= '<li><a href="[acp]boost/section/' . $service . '"><i class="fa fa-list-ol"></i> ' . $aBoost['cs'][$service]['site'] . '</a></li>';
}

$html->set('boosts', $boosts);

$html->pack('menu');

$inc = $section != 'index' ? 'service' : 'index';

require(SEC . 'boost/' . $inc . '.php');
