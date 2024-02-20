<?php
if (!DEFINED('EGP')) {
    header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404');
    exit();
}

// Подключаем разделы
if (isset($url['id'])) {
    require_once SEC . 'monitoring/server.php';
} else {
    require_once SEC . 'monitoring/all.php';
}