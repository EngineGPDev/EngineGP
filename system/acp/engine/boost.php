<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @link      https://github.com/EngineGPDev/EngineGP
 * @link      https://gitforge.ru/EngineGP/EngineGP
 * @copyright Copyright (c) Solovev Sergei <inbox@seansolovev.ru>
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

include(DATA . 'boost.php');

$info = '<i class="fa fa-cloud"></i> Статистика BOOST CS: 1.6';

$aSection = $aBoost['cs']['boost'];

if ($section == 'search')
    include(SEC . 'boost/search.php');

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

include(SEC . 'boost/' . $inc . '.php');
