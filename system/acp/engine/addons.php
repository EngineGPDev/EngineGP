<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$info = '<i class="fa fa-cubes"></i> Управление дополнениями';

$aSection = ['index', 'update', 'addcat', 'addpl', 'cats', 'updmp', 'delete'];

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

require(SEC . 'addons/' . $section . '.php');
