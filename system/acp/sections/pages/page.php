<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

$sql->query('SELECT `name`, `file` FROM `pages` WHERE `id`="' . $id . '" LIMIT 1');
$page = $sql->get();

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : $page['name'];
    $aData['text'] = isset($_POST['text']) ? trim((string) $_POST['text']) : file_get_contents(FILES . 'pages/' . $page['file']);

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    $file = fopen(FILES . 'pages/' . $page['file'], "w");

    fputs($file, $aData['text']);

    fclose($file);

    $sql->query('UPDATE `pages` set `name`="' . htmlspecialchars((string) $aData['name']) . '" WHERE `id`="' . $id . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
}

$html->get('page', 'sections/pages');

$html->set('id', $id);
$html->set('name', htmlspecialchars_decode((string) $page['name']));

$html->set('text', htmlspecialchars(file_get_contents(FILES . 'pages/' . $page['file'])));

$html->pack('main');
