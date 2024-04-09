<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$info = '<i class="fa fa-globe"></i> Список вирт. хостингов';

$html->get('menu', 'sections/hosting');
$html->pack('menu');
