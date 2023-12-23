<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$title = 'Договор оферты';
$html->nav($title);

$html->get('agreement');
$html->pack('main');
?>