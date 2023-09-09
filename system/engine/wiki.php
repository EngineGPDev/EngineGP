<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$title = 'Ответы на вопросы';

if (in_array($section, array('answer', 'question', 'search', 'msearch')))
    require(SEC . 'wiki/' . $section . '.php');
else
    require(SEC . 'wiki/index.php');
