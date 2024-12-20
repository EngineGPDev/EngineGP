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

if ($id) {
    include(SEC . 'units/unit.php');
} else {
    $list = '';

    $sql->query('SELECT `id`, `name`, `address`, `show`, `domain` FROM `units` ORDER BY `id` ASC');
    while ($unit = $sql->get()) {
        $list .= '<tr>';
        $list .= '<td>' . $unit['id'] . '</td>';
        $list .= '<td><a href="' . $cfg['http'] . 'acp/units/id/' . $unit['id'] . '">' . $unit['name'] . '</a></td>';
        $list .= '<td>' . $unit['address'] . '</td>';
        $list .= '<td>' . ($unit['show'] == '1' ? 'Доступна' : 'Недоступна') . '</td>';
        $list .= '<td>' . $unit['domain'] . '</td>';
        $list .= '<td><a href="#" onclick="return units_delete(\'' . $unit['id'] . '\')" class="text-red">Удалить</a></td>';
        $list .= '</tr>';
    }

    $html->get('index', 'sections/units');

    $html->set('list', $list);

    $html->pack('main');
}
