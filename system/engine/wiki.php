<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$title = 'Ответы на вопросы';

if (in_array($section, array('answer', 'question', 'search', 'msearch')))
    include(SEC . 'wiki/' . $section . '.php');
else
    include(SEC . 'wiki/index.php');
?>