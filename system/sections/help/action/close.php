<?php
if (!defined('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

if ($user['group'] == 'support')
    sys::outjs(['e' => 'У вас нет доступа к данному действию.']);

if ($id) {
    if (in_array($user['group'], ['admin', 'support']))
        $sql->query('UPDATE `help` set `close`="1" WHERE `id`="' . $id . '" LIMIT 1');
    else
        $sql->query('UPDATE `help` set `close`="1" WHERE `id`="' . $id . '" AND `user`="' . $user['id'] . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
}

sys::outjs(['e' => 'Вопрос не найден в базе.']);
