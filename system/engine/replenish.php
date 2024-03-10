<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

if (!isset($url['response']) and !in_array($url['response'], array('success', 'fail')))
    exit();

if ($url['response'] == 'success')
    sys::out(file_get_contents(ROOT . 'success.html'));
else
    sys::out(file_get_contents(ROOT . 'fail.html'));
