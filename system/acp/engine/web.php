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

$info = '<i class="fa fa-cloud"></i> Список бесплатных услуг';

$aSection = [
    'index',
    'csbans',
    'csstats',
    'astats',
    'sourcebans',
    'mysql',
    'hosting',
];

if (!in_array($section, $aSection)) {
    $section = 'index';
}

$html->get('menu', 'sections/web');

$html->unit('s_' . $section, true);

unset($aSection[array_search($section, $aSection)]);

foreach ($aSection as $noactive) {
    $html->unit('s_' . $noactive);
}

$sql->query('SELECT `id` FROM `web`');
$html->set('all', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="amxbans"');
$html->set('amxbans', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="csbans"');
$html->set('csbans', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="psychostats"');
$html->set('psychostats', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="csstats"');
$html->set('csstats', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="astats"');
$html->set('astats', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="sourcebans"');
$html->set('sourcebans', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="rankme"');
$html->set('rankme', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="mysql"');
$html->set('mysql', $sql->num());

$sql->query('SELECT `id` FROM `web` WHERE `type`="hosting"');
$html->set('hosting', $sql->num());

$html->pack('menu');

include(SEC . 'web/' . $section . '.php');
