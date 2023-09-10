<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$info = '<i class="fa fa-envelope-open"></i> Рассылка новостей';

$aSection = ['index', 'send'];

if (!in_array($section, $aSection))
    $section = 'index';

$html->get('menu', 'sections/letter');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive)
    $html->unit('s_' . $noactive);

$html->pack('menu');

require(SEC . 'letter/' . $section . '.php');
