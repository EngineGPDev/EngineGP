<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if ($go) {
    $aData = [];

    $aData['name'] = isset($_POST['name']) ? trim((string) $_POST['name']) : '';
    $aData['text'] = isset($_POST['text']) ? trim((string) $_POST['text']) : '';

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля']);

    $name = md5(time() . random_int(5, 100) . random_int(10, 20) . random_int(1, 20) . random_int(40, 80));

    $file = fopen(FILES . 'pages/' . $name, "w");

    fputs($file, $aData['text']);

    fclose($file);

    $sql->query('INSERT INTO `pages` set `name`="' . htmlspecialchars($aData['name']) . '", `file`="' . $name . '"');

    sys::outjs(['s' => 'ok']);
}

$html->get('add', 'sections/pages');

$html->pack('main');
