<?php
if(!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

$info = '<i class="fa fa-puzzle-piece"></i> Управление плагинами';

$html->get('menu', 'sections/plugins');




$html->pack('menu');

include(SEC.'plugins/'.$section.'.php');










if ($_FILES['plugin']['error'] === UPLOAD_ERR_OK) {
    $zip = new ZipArchive();
    $res = $zip->open($_FILES['plugin']['tmp_name']);
    if ($res === true) {
        $zip->extractTo('/path/to/plugins/');
        $zip->close();
        // Проверяем, что в архиве есть файлы, необходимые для работы плагина
        if (file_exists('/path/to/plugins/plugin.php')) {
            // Загружаем класс плагина
            require_once '/path/to/plugins/plugin.php';
            // Создаем экземпляр класса плагина
            $plugin = new Plugin();
            // Регистрируем плагин в системе
            PluginLoader::registerPlugin($plugin);
        }
    }
}