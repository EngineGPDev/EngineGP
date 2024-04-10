<?php
/*
 * EngineGP   (https://enginegp.ru or https://enginegp.com)
 *
 * @copyright Copyright (c) 2018-present Solovev Sergei <inbox@seansolovev.ru>
 *
 * @link      https://github.com/EngineGPDev/EngineGP for the canonical source repository
 * @link      https://gitforge.ru/EngineGP/EngineGP for the canonical source repository
 *
 * @license   https://github.com/EngineGPDev/EngineGP/blob/main/LICENSE MIT License
 * @license   https://gitforge.ru/EngineGP/EngineGP/src/branch/main/LICENSE MIT License
 */

if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));

if (isset($url['delete'])) {
    $sql->query('DELETE FROM `promo_use` WHERE `id`="' . sys::int($url['delete']) . '" LIMIT 1');

    sys::outjs(array('s' => 'ok'));
}

$list = '';

$all_use = $sql->query('SELECT * FROM `promo_use` ORDER BY `id` ASC LIMIT 100');
while ($promo_use = $sql->get($all_use)) {
    $sql->query('SELECT `text` FROM `logs` WHERE `user`="' . $promo_use['user'] . '" AND `date`="' . $promo_use['time'] . '" LIMIT 1');
    $log = $sql->get();

    $sql->query('SELECT `cod` FROM `promo` WHERE `id`="' . $promo_use['promo'] . '" LIMIT 1');
    $promo = $sql->get();

    $list .= '<tr>';
    $list .= '<td>' . $promo_use['id'] . '</td>';
    $list .= '<td><a href="' . $cfg['http'] . 'acp/promo/id/' . $promo_use['id'] . '">' . $promo['cod'] . '</a></td>';
    $list .= '<td class="text-center"><a href="' . $cfg['http'] . 'acp/users/id/' . $promo_use['user'] . '">USER_' . $promo_use['user'] . '</a></td>';
    $list .= '<td>' . $log['text'] . '</td>';
    $list .= '<td class="text-center">' . sys::today($promo_use['time']) . '</td>';
    $list .= '<td class="text-center"><a href="#" onclick="return promo_use_delete(\'' . $promo_use['id'] . '\')" class="text-red">Удалить</a></td>';
    $list .= '</tr>';
}

$html->get('stats', 'sections/promo');

$html->set('list', $list);

$html->pack('main');
