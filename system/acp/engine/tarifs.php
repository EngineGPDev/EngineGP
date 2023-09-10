<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$info = '<i class="fa fa-shopping-bag"></i> Управление тарифами';

$aSection = ['index', 'add', 'copy', 'stats', 'delete'];

if (!in_array($section, $aSection))
    $section = 'index';

$html->get('menu', 'sections/tarifs');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive)
    $html->unit('s_' . $noactive);

$sql->query('SELECT `id` FROM `tarifs`');
$html->set('tarifs', $sql->num());

$html->pack('menu');

require(SEC . 'tarifs/' . $section . '.php');
