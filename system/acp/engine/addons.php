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

$info = '<i class="fa fa-cubes"></i> Управление дополнениями';

$aSection = array(
    'index',
    'update',
    'addcat',
    'addpl',
    'cats',
    'updmp',
    'delete'
);

if (!in_array($section, $aSection))
    $section = 'index';

$html->get('menu', 'sections/addons');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive)
    $html->unit('s_' . $noactive);

$sql->query('SELECT `id` FROM `plugins_category`');
$html->set('cats', $sql->num());

$sql->query('SELECT `id` FROM `plugins`');
$html->set('plugins', $sql->num());

$html->pack('menu');

include(SEC . 'addons/' . $section . '.php');
