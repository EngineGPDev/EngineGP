<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if ($go) {
    $aData = [];

    $data = ['name', 'job', 'desc', 'status'];
    foreach ($data as $idata)
        $aData[$idata] = $_POST[$idata] ?? '';

    if (in_array('', $aData))
        sys::outjs(['e' => 'Необходимо заполнить все поля!']);

    $sql->query('INSERT INTO `jobs` set'
        . '`name`="' . $aData['name'] . '",'
        . '`job`="' . $aData['job'] . '",'
        . '`desc`="' . $aData['desc'] . '",'
        . '`status`="' . $aData['status'] . '",'
        . '`date`="' . $start_point . '"');

    sys::outjs(['s' => 'ok']);
}

$html->get('add', 'sections/jobs');
$html->pack('main');
