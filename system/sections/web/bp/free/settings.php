<?php

/*
 * Copyright 2018-2025 Solovev Sergei
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

use Symfony\Component\Dotenv\Dotenv;

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

// Загружаем .env
$dotenv = new Dotenv();
$dotenv->load(ROOT . '/.env');

if ($_ENV['RUN_MODE'] === 'dev') {
    // Включение отображения ошибок в режиме разработки
    ini_set('display_errors', true);
    ini_set('html_errors', true);
    ini_set('error_reporting', E_ALL);
} else {
    // Отключение отображения ошибок в продакшене
    ini_set('display_errors', false);
    ini_set('html_errors', false);
    ini_set('error_reporting', 0);
}

switch ($aWebInstall[$server['game']][$url['subsection']]) {
    case 'server':
        $sql->query('SELECT `id`, `domain` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `server`="' . $id . '" LIMIT 1');

        break;

    case 'user':
        $sql->query('SELECT `id`, `domain` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" LIMIT 1');

        break;

    case 'unit':
        $sql->query('SELECT `id`, `domain` FROM `web` WHERE `type`="' . $url['subsection'] . '" AND `user`="' . $server['user'] . '" AND `unit`="' . $server['unit'] . '" LIMIT 1');
}

if (!$sql->num()) {
    exit;
}

$web = $sql->get();

include(DATA . 'web.php');

include(LIB . 'web/free.php');
include(LIB . 'ssh.php');

$unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('error', 'ssh')], $name_mcache);
}

// Директория дополнительной услуги
$install = $aWebUnit['install'][$aWebUnit['unit'][$url['subsection']]][$url['subsection']] . $web['domain'];

$unit = web::unit($aWebUnit, $aData['type'], $web['unit']);

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    sys::outjs(['e' => sys::text('error', 'ssh')], $name_mcache);
}

$arSel = ['$adm_login', '$adm_pass', '$wmr_on', '$purse', '$secret_key', '$to', '$vk', '$skype'];

$conf = explode("\n", $ssh->get('cat ' . $install . '/core/cfg.php'));

$aData = [];

if ($go) {
    $aData['bp_login'] = isset($_POST['bp_login']) ? trim($_POST['bp_login']) : '';
    $aData['bp_passwd'] = isset($_POST['bp_passwd']) ? trim($_POST['bp_passwd']) : '';
    $aData['bp_mail'] = isset($_POST['bp_mail']) ? trim($_POST['bp_mail']) : '';
    $aData['bp_vk'] = isset($_POST['bp_vk']) ? trim($_POST['bp_vk']) : '';
    $aData['bp_skype'] = isset($_POST['bp_skype']) ? trim($_POST['bp_skype']) : '';
    $aData['bp_webmoney'] = $_POST['bp_webmoney'] ?? '0';
    $aData['bp_wmr'] = isset($_POST['bp_wmr']) ? trim($_POST['bp_wmr']) : '';
    $aData['bp_sign_key'] = isset($_POST['bp_sign_key']) ? trim($_POST['bp_sign_key']) : '';

    $aData['bp_webmoney'] = $aData['bp_webmoney'] == 'on' ? '1' : '0';

    foreach ($aData as $var => $val) {
        $aData[$var] = str_replace('"', '', $val);
    }

    $str_search = [
        '#\$adm_login = ".*"#iu',
        '#\$adm_pass = ".*"#iu',
        '#\$to = ".*"#iu',
        '#\$vk = ".*"#iu',
        '#\$skype = ".*"#iu',
        '#\$wmr_on = ".*"#iu',
        '#\$purse = ".*"#iu',
        '#\$secret_key = ".*"#iu',
    ];

    $str_replace = [
        '$adm_login = "' . $aData['bp_login'] . '"',
        '$adm_pass = "' . $aData['bp_passwd'] . '"',
        '$to = "' . $aData['bp_mail'] . '"',
        '$vk = "' . $aData['bp_vk'] . '"',
        '$skype = "' . $aData['bp_skype'] . '"',
        '$wmr_on = "' . $aData['bp_webmoney'] . '"',
        '$purse = "' . $aData['bp_wmr'] . '"',
        '$secret_key = "' . $aData['bp_sign_key'] . '"',
    ];

    $data = '';

    foreach ($conf as $line => $val) {
        if ($val == '<?php') {
            $data .= '<?php';

            continue;
        }

        $data .= "\n" . preg_replace($str_search, $str_replace, $val);
    }

    $temp = sys::temp($data);

    $ssh->setfile($temp, $install . '/core/cfg.php');
    $ssh->set('chmod 0644' . ' ' . $install . '/core/cfg.php');

    sys::outjs(['s' => 'ok'], $name_mcache);
}

foreach ($conf as $str) {
    $aStr = explode('=', $str);

    if (!isset($aStr[0]) || !isset($aStr[1])) {
        continue;
    }

    $var = trim($aStr[0]);
    $val = str_replace(['"', ';'], '', trim($aStr[1]));

    if (!in_array($var, $arSel)) {
        continue;
    }

    $aData[$var] = isset($val[0]) ? $val : '';
}

$webmoney = $aData['$wmr_on'] == 'on' ? '<option value="on">Включено</option><option value="off">Выключено</option>' : '<option value="off">Выключено</option><option value="on">Включено</option>';

sys::outjs([
    's' => 'ok',
    'bp_login' => $aData['$adm_login'],
    'bp_passwd' => $aData['$adm_pass'],
    'bp_mail' => $aData['$to'],
    'bp_vk' => $aData['$vk'],
    'bp_skype' => $aData['$skype'],
    'bp_wmr' => $aData['$purse'],
    'bp_sign_key' => $aData['$secret_key'],
    'bp_webmoney' => $webmoney], $name_mcache);
