<?php

/*
 * Copyright 2018-2024 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Проверка на авторизацию
sys::noauth();

$updir = ROOT . 'upload/';

$file = $_POST['value'] ?? exit;
$name = $_POST['name'] ?? exit;

$pname = explode('.', $name);
$type = strtolower(end($pname));

if (!in_array($type, ['png', 'gif', 'jpg', 'jpeg', 'bmp'])) {
    exit('Допустимый формат изображений: png, gif, jpg, jpeg, bmp.');
}

$aData = explode(',', $file);

$rdmName = md5($start_point . sys::passwd(10) . $user['id']) . '.' . $type;

if (file_put_contents($updir . $rdmName, base64_decode(str_replace(' ', '+', $aData[1])))) {
    $sql->query('INSERT INTO `help_upload` set `user`="' . $user['id'] . '", `name`="' . $rdmName . '", `time`="' . $start_point . '", `status`="0"');

    exit($rdmName . ':ok');
}

exit('Ошибка загрузки: убедитесь, что изображение не повреждено и имеет правильный формат.');
