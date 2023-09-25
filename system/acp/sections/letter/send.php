<?php
use EngineGP\System\Library\Acp\sys;

if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

set_time_limit(1200);

$aData = [];

$aData['title'] = isset($_POST['title']) ? trim((string) $_POST['title']) : sys::outjs(['e' => 'Необходимо указать заголовок']);
$aData['text'] = isset($_POST['text']) ? trim((string) $_POST['text']) : sys::outjs(['e' => 'Необходимо указать сообщение']);

$aData['users'] = $_POST['users'] ?? sys::outjs(['e' => 'Необходимо указать получателей']);

if ($aData['title'] == '' || $aData['text'] == '')
    sys::outjs(['e' => 'Необходимо заполнить все поля']);

if (!is_array($aData['users']) || !count($aData['users']))
    sys::outjs(['e' => 'Необходимо указать минимум одного получателя']);

$noletter = '';

require(LIB . 'smtp.php');

foreach ($aData['users'] as $id => $cheked) {
    if ($cheked != 'on')
        continue;

    $sql->query('SELECT `mail` FROM `users` WHERE `id`="' . sys::int($id) . '" LIMIT 1');
    $us = $sql->get();

    $tpl = file_get_contents(DATA . 'mail.ini', "r");

    $text = str_replace(
        ['[name]', '[text]', '[http]', '[img]', '[css]'],
        [$cfg['name'], $aData['text'], $cfg['http'], $cfg['http'] . 'template/images/', $cfg['http'] . 'template/css/'],
        $tpl
    );

    $smtp = new smtp($cfg['smtp_login'], $cfg['smtp_passwd'], $cfg['smtp_url'], $cfg['smtp_mail'], 465);

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: " . $cfg['smtp_name'] . " <" . $cfg['smtp_mail'] . ">\r\n";

    if (!$smtp->send($us['mail'], strip_tags((string) $aData['title']), $text, $headers))
        $noletter .= '<p>' . $us['mail'] . '</p>';
}

if ($noletter == '')
    $noletter = 'отправлено всем.';

sys::outjs(['s' => $noletter]);
