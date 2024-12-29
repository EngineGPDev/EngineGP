<?php

/*
 * Copyright 2018-2024 Solovev Sergei
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if (!defined('EGP')) {
    exit(header('Refresh: 0; URL=http://' . $_SERVER['HTTP_HOST'] . '/404'));
}

if (isset($url['delete'])) {
    $sql->query('DELETE FROM `promo_use` WHERE `id`="' . sys::int($url['delete']) . '" LIMIT 1');

    sys::outjs(['s' => 'ok']);
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
