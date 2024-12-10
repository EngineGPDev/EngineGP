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

$info = '<i class="fa fa-bullhorn"></i> Управление уведомлениями';

$aSection = [
    'index',
    'add',
    'end',
    'delete',
];

if (!in_array($section, $aSection)) {
    $section = 'index';
}

$html->get('menu', 'sections/notice');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive) {
    $html->unit('s_' . $noactive);
}

$sql->query('SELECT `id` FROM `notice` WHERE `time`>"' . $start_point . '"');
$html->set('active', $sql->num());

$html->pack('menu');

include(SEC . 'notice/' . $section . '.php');
