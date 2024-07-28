<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 */

if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

$html->nav('Параметры server.cfg');

$sql->query('SELECT `address`, `passwd` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `install` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

include(LIB . 'ssh.php');

if (!$ssh->auth($unit['passwd'], $unit['address'])) {
    if ($go)
        sys::outjs(array('e' => sys::text('error', 'ssh')), $nmch);

    sys::back($cfg['http'] . 'servers/id/' . $id . '/section/settings');
}

include(DATA . 'scfg/' . $server['game'] . '.php');

// Сохранение изменений
if ($go) {
    $servercfg = isset($_POST['config']) ? $_POST['config'] : '';

    $config = '';

    foreach ($servercfg as $cvar => $val)
        if ($val != '')
            $config .= str_replace("'", '', $cvar) . '=' . $val . PHP_EOL;

    // Временый файл
    $temp = sys::temp($config);

    $ssh->setfile($temp, $tarif['install'] . $server['uid'] . '/server.properties');
    $ssh->set('chmod 0644' . ' ' . $tarif['install'] . $server['uid'] . '/server.properties');

    $ssh->set('chown server' . $server['uid'] . ':servers ' . $tarif['install'] . $server['uid'] . '/server.properties');

    unlink($temp);

    sys::outjs(array('s' => 'ok'), $nmch);
}

// Чтение файла - server.properties
$file = $tarif['install'] . $server['uid'] . '/server.properties';

$ssh->set('echo "" >> ' . $file . ' && cat ' . $file . ' | grep -ve "^#\|^[[:space:]]*$"');

$fScfg = explode("\n", strip_tags($ssh->get()));

$servercfg = array();

// Убираем пробелы и генерируем массив
foreach ($fScfg as $line) {
    // имя квара
    $cvar = sys::first(explode('=', $line));

    if ($cvar == '')
        continue;

    // убираем имя квара и оставляем только значение
    $value = str_replace($cvar . '=', '', $line);

    // выбираем только то, что нам нужно
    preg_match_all('~([^"]+)~', $value, $cvar_value, PREG_SET_ORDER);

    // Исключаем комментарии
    if ($cvar == '//')
        continue;

    $val = sys::first(explode(' //', $cvar_value[0][1]));

    // Добавляем данные в массив
    if (array_key_exists($cvar, $aScfg))
        $servercfg[$cvar] = trim($val);
}

foreach ($aScfg as $name => $desc) {
    if (!isset($servercfg[$name]))
        $servercfg[$name] = '';

    // Формирование формы
    if (strpos($aScfg_form[$name], 'select'))
        $form = str_replace('value="' . $servercfg[$name] . '"', 'value="' . $servercfg[$name] . '" selected="select"', $aScfg_form[$name]);
    else
        $form = str_replace('[' . $name . ']', $servercfg[$name], $aScfg_form[$name]);

    $html->get('servercfg_list', 'sections/servers/games/settings');

    $html->set('name', $name);
    $html->set('desc', $desc);
    $html->set('form', $form);

    $html->pack('list');
}

$html->get('servercfg', 'sections/servers/' . $server['game'] . '/settings');

$html->set('id', $id);
$html->set('cfg', $html->arr['list']);

$html->pack('main');
