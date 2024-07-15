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

set_time_limit(1200);

$aData = array();

$aData['title'] = isset($_POST['title']) ? trim($_POST['title']) : sys::outjs(array('e' => 'Необходимо указать заголовок'));
$aData['text'] = isset($_POST['text']) ? trim($_POST['text']) : sys::outjs(array('e' => 'Необходимо указать сообщение'));
$aData['users'] = isset($_POST['users']) ? $_POST['users'] : sys::outjs(array('e' => 'Необходимо указать получателей'));

if ($aData['title'] == '' || $aData['text'] == '')
    sys::outjs(array('e' => 'Необходимо заполнить все поля'));

if (!is_array($aData['users']) || !count($aData['users']))
    sys::outjs(array('e' => 'Необходимо указать минимум одного получателя'));

$noletter = '';

include(LIB . 'smtp.php');

foreach ($aData['users'] as $id => $cheked) {
    if ($cheked != 'on')
        continue;

    $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . sys::int($id) . '" LIMIT 1');
    $us = $sql->get();

    $tpl = file_get_contents(DATA . 'mail.ini');

    $text = str_replace(
        array('[name]', '[text]', '[http]', '[img]', '[css]'),
        array($cfg['name'], $aData['text'], $cfg['http'], $cfg['http'] . 'template/images/', $cfg['http'] . 'template/css/'),
        $tpl
    );

    $smtp = new smtp();

    if (!$smtp->send($us['mail'], strip_tags($aData['title']), $text))
        $noletter .= '<p>' . $us['mail'] . '</p>';
}

if ($noletter == '')
    $noletter = 'отправлено всем.';

sys::outjs(array('s' => $noletter));
