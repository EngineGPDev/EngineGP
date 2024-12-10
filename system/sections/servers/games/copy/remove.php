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

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

$cid = isset($url['cid']) ? sys::int($url['cid']) : sys::outjs(['e' => 'Выбранная копия не найдена.'], $nmch);

$sql->query('SELECT `name`, `status` FROM `copy` WHERE `id`="' . $cid . '" AND `user`="' . $server['user'] . '_' . $server['unit'] . '" AND `game`="' . $server['game'] . '" LIMIT 1');
if (!$sql->num()) {
    sys::outjs(['e' => 'Выбранная копия не найдена.'], $nmch);
}

$copy = $sql->get();

if (!$copy['status']) {
    sys::outjs(['e' => 'Дождитесь создания резервной копии.'], $nmch);
}

$ssh->set('tmux new-session -ds rem_copy_' . $cid . ' rm /copy/' . $copy['name'] . '.tar');

$sql->query('DELETE FROM `copy` WHERE `id`="' . $cid . '" LIMIT 1');

// Очистка кеша
$mcache->delete('server_copy_' . $id);

sys::outjs(['s' => 'ok'], $nmch);
