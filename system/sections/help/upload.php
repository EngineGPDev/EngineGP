<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

// Проверка на авторизацию
sys::noauth();

$updir = DIR . 'upload/';

$file = $_POST['value'] ?? exit;
$name = $_POST['name'] ?? exit;

$pname = explode('.', (string) $name);
$type = strtolower(end($pname));

if (!in_array($type, ['png', 'gif', 'jpg', 'jpeg', 'bmp']))
    exit('Допустимый формат изображений: png, gif, jpg, jpeg, bmp.');

$aData = explode(',', (string) $file);

$rdmName = md5($start_point . sys::passwd(10) . $user['id']) . '.' . $type;

if (file_put_contents($updir . $rdmName, base64_decode(str_replace(' ', '+', $aData[1])))) {
    $sql->query('INSERT INTO `help_upload` set `user`="' . $user['id'] . '", `name`="' . $rdmName . '", `time`="' . $start_point . '", `status`="0"');

    exit($rdmName . ':ok');
}

exit('Ошибка загрузки: убедитесь, что изображение не повреждено и имеет правильный формат.');
