<?php
if (!defined('EGP')) {
    header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404');
    exit();
}

// Подключаем разделы
if (isset($url['id'])) {
    require SEC . 'monitoring/server.php';
} else {
    require SEC . 'monitoring/all.php';
}