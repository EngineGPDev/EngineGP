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
