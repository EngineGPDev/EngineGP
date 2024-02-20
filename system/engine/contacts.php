<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$title = 'Контактная информация';

$html->nav($title);

$html->get('contacts');
$html->pack('main');
?>