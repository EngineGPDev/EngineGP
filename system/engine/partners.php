<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$title = 'Партнеры';
$html->nav($title);

$html->get('partners');
$html->pack('main');
