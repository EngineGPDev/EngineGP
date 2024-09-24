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

$info = '<i class="fa fa-clipboard"></i> Логи операций';

$aSection = array(
    'index',
    'buy',
    'extend',
    'boost',
    'cashout',
    'part',
    'search',
    'replenish'
);

if (!in_array($section, $aSection)) {
    $section = 'index';
}

$html->get('menu', 'sections/logs');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive) {
    $html->unit('s_' . $noactive);
}

$html->pack('menu');

include(SEC . 'logs/' . $section . '.php');
