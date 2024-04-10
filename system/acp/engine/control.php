<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$info = '<i class="fa-brands fa-dropbox"></i> Контроль';

$aSection = array(
    'index',
    'overdue',
    'delete'
);

if (!in_array($section, $aSection))
    $section = 'index';

$del = $cfg['server_delete'] * 86400;
$time = $start_point - $del;

$html->get('menu', 'sections/control');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive)
    $html->unit('s_' . $noactive);

$sql->query('SELECT `id` FROM `control` WHERE `user`!="-1"');
$html->set('all', $sql->num());

$sql->query('SELECT `id` FROM `control` WHERE `user`!="-1" AND `time`<"' . $start_point . '" AND `overdue`>"' . $time . '"');
$html->set('overdue', $sql->num());

$html->pack('menu');

include(SEC . 'control/' . $section . '.php');
