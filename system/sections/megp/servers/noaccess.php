<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

if ($server['time'] < $start_point)
    $html->get('overdue');
else {
    $status = array(
        'install' => 'установки',
        'reinstall' => 'переустановки',
        'update' => 'обновления',
        'recovery' => 'восстановления',
        'blocked' => 'блокировки'
    );

    $html->get('noaccess');

    $html->set('status', $status[$server['status']]);
}

$html->pack('main');
?>