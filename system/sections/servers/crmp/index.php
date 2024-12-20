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

$sql->query('SELECT `unit`, `tarif`, `slots_start`, `online`, `players`, `name`, `pack`, `map`, `time`, `date`, `overdue` FROM `servers` WHERE `id`="' . $id . '" LIMIT 1');
$server = array_merge($server, $sql->get());

$html->nav($server['address'] . ':' . $server['port']);

$sql->query('SELECT `name` FROM `units` WHERE `id`="' . $server['unit'] . '" LIMIT 1');
$unit = $sql->get();

$sql->query('SELECT `name`, `packs` FROM `tarifs` WHERE `id`="' . $server['tarif'] . '" LIMIT 1');
$tarif = $sql->get();

$btn = sys::buttons($id, $server['status'], $server['game']);

$time_end = $server['status'] == 'overdue' ? 'Удаление через: ' . sys::date('min', $server['overdue'] + $cfg['server_delete'] * 86400) : 'Осталось: ' . sys::date('min', $server['time']);

$html->get('index', 'sections/servers/' . $server['game']);

$html->set('id', $id);
$html->set('unit', $unit['name']);
$html->set('tarif', $tarif['name']);

$tarif['packs'] = sys::b64djs($tarif['packs']);

$html->set('pack', $tarif['packs'][$server['pack']]);
$html->set('address', $server['address'] . ':' . $server['port']);
$html->set('game', $aGname[$server['game']]);
$html->set('slots', $server['slots_start']);
$html->set('online', $server['online']);
$html->set('players', base64_decode($server['players'] ?? ''));
$html->set('name', $server['name']);
$html->set('status', sys::status($server['status'], $server['game'], $server['map']));
$html->set('img', sys::status($server['status'], $server['game'], 'crmp', 'img'));
$html->set('time_end', $time_end);
$html->set('time', sys::today($server['time']));
$html->set('date', sys::today($server['date']));

$html->set('btn', $btn);

$html->pack('main');
