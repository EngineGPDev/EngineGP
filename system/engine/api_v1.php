<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$title = 'API интерфейс';
$html->nav($title);

$html->get('api');
$html->pack('main');
