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

$aScfg = array(
    'hostname' => 'Название игрового сервера.',
    'rcon_password' => 'Пароль для упраления сервером через RCON команды.',
    'sv_password' => 'Пароль для входа на сервер.',
    'decalfrequency' => 'Установить как часто игрок может рисовать свою эмблему. Значения выставляются в секундах.',
    'allow_spectators' => 'Максимальное количество наблюдателей на сервере. Поставтьте 0, что бы выключить.',
    'mp_autokick' => 'Включает автоматический бан для тимкиллеров и кикает подвисших игроков.',
    'mp_autoteambalance' => 'Автобаланс игроков.',
    'mp_chattime' => 'Количество секунд на которое сервер позволяет игрокам писать в общий чат после конца карты и до загрузки новой.',
    'mp_decals' => 'Определяет количество декалов на сервере (напр кол-во дырок от пуль на стенах сохраняемых сервером).',
    'mp_fadetoblack' => 'Погибшие игроки не могут наблюдать за игрой (черный экран).',
    'mp_flashlight' => 'Разрешить использовать фонарик.',
    'mp_footsteps' => 'Разрешить игрокам слышать звук шагов.',
    'mp_forcecamera' => 'Задает также параметры наблюдения.',
    'mp_forcechasecam' => 'Команда задает режим наблюдения за игроками.',
    'mp_fraglimit' => 'Лимит убийств игрока до смены карты. 0 - Отключено.',
    'mp_freezetime' => 'Время заморозки игроков перед началом раунда. 0 - Отключено.',
    'mp_friendlyfire' => 'Нанесение повреждений игрокам своей команды.',
    'mp_hostagepenalty' => 'Устанавливает количество заложников, которых можно убить, прежде чем вас выкинет с сервера. 0 - Отключено.',
    'mp_limitteams' => 'Число игроков, на которое одна команда может превышать другую. Лишних игроков не будет пускать в команду. 0 - Отключено.',
    'mp_maxrounds' => 'Максимальное количество раундов, которые могут быть сыграны на карте до ее смены.',
    'mp_playerid' => 'Всплывающие подсказки при наведении прицела на игрока.',
    'mp_roundtime' => 'Время раунда. Измеряется в минутах.',
    'mp_startmoney' => 'Количество денег у игрока в первом раунде.',
    'mp_timelimit' => 'Время, отведенное на одну карту. Измеряется в минутах. Значение 0 - отключение лимита времени.',
    'mp_tkpunish' => 'В начале нового раунда убивает игрока, который в предыдущем убил игрока своей команды.',
    'mp_winlimit' => 'Ограничение по победам на карту.',
    'sv_allowdownload' => 'Возможность скачивать с сервера файлы.',
    'sv_allowupload' => 'Возможность закачивать на сервер файлы. Например спрэи.',
    'sv_alltalk' => 'Режим работы общего голосового чата. При включении все игроки будут слышать друг друга независимо от команды, в которой они играют.',
    'sv_gravity' => 'Гравитация на сервере. По умолчанию 800.',
    'sv_maxspeed' => 'Максимальная скорость передвижения игроков на сервере. По умолчанию 320.',
    'sv_maxunlag' => 'Максимальная лаго компенсация в секунду.',
    'pausable' => 'Возможность использования паузы во время игры.',
    'sv_voiceenable' => 'Возможность использовать микрофон в игре.',
    'mp_c4timer' => 'Время таймера до взрыва бомбы с момента ее установки. Измеряется в секундах.',
    'mp_consistency' => 'Проверка соответствия файлов сервера и клиента.',
    'mp_buytime' => 'Время для закупки оружия в начале раунда. Значение выставляется в минутах.',
    'sv_contact' => 'Контакты админа.',
    'sv_rcon_banpenalty' => 'Колличество минут на которое банится игрок пытавшийся подобрать rcon-пароль к серверу.',
    'sv_rcon_maxfailures' => 'Максимальное колличество попыток при наборе rcon-пароля, после истечения которых игрок будет забанен.',
    'sv_rcon_minfailures' => 'Колличество попыток при наборе rcon-пароля во время заданное sv_rcon_minfailuretime, после истечения которых игрок будет забанен.',
    'sv_rcon_minfailuretime' => 'Колличество секунд для определения неверной rcon-аутенфикации.',
    'sv_maxrate' => 'Максимально-допустимый предел передачи/приёма байт в секунду между клиентом и сервером.',
    'sv_maxupdaterate' => 'Максимальное количество переданных пакетов в секунду.',
    'sv_minrate' => 'Минимально допустимый предел передачи/приёма байт в секунду между клиентом и сервером.',
    'sv_minupdaterate' => 'Минимальное количество переданных пакетов в секунду.',
    'mp_mapvoteratio' => 'Процент голосование за следующую карту, командой votemap.'
);

$aScfg_form = array(
    'hostname' => '<input value="[hostname]" name="config[\'hostname\']">',
    'rcon_password' => '<input value="[rcon_password]" name="config[\'rcon_password\']">',
    'sv_password' => '<input value="[sv_password]" name="config[\'sv_password\']">',
    'decalfrequency' => '<input value="[decalfrequency]" name="config[\'decalfrequency\']">',
    'allow_spectators' => '<select name="config[\'allow_spectators\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'mp_autokick' => '<select name="config[\'mp_autokick\']"><option value="0">Выключено</option><option value="1">Включено</option></select>',
    'mp_autoteambalance' => '<select name="config[\'mp_autoteambalance\']"><option value="0">Выключено</option><option value="1">Включено</option></select>',
    'mp_chattime' => '<input value="[mp_chattime]" name="config[\'mp_chattime\']">',
    'mp_decals' => '<input value="[mp_decals]" name="config[\'mp_decals\']">',
    'mp_fadetoblack' => '<select name="config[\'mp_fadetoblack\']"><option value="0">Выключено</option><option value="1">Включено</option></select>',
    'mp_flashlight' => '<select name="config[\'mp_flashlight\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'mp_footsteps' => '<select name="config[\'mp_footsteps\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'mp_forcecamera' => '<select name="config[\'mp_forcecamera\']"><option value="0">можно следить за всеми, за своей командой и противником</option><option value="1">только на своей командой</option><option value="2">только за своей командой от 1-ого лица</option></select>',
    'mp_fraglimit' => '<input value="[mp_fraglimit]" name="config[\'mp_fraglimit\']">',
    'mp_freezetime' => '<input value="[mp_freezetime]" name="config[\'mp_freezetime\']">',
    'mp_friendlyfire' => '<select name="config[\'mp_friendlyfire\']"><option value="0">Выключено</option><option value="1">Включено</option></select>',
    'mp_hostagepenalty' => '<select name="config[\'mp_hostagepenalty\']"><option value="0">Выключено</option><option value="1">Включено</option></select>',
    'mp_limitteams' => '<input value="[mp_limitteams]" name="config[\'mp_limitteams\']">',
    'mp_maxrounds' => '<input value="[mp_maxrounds]" name="config[\'mp_maxrounds\']">',
    'mp_playerid' => '<select name="config[\'mp_playerid\']"><option value="0">Включено</option><option value="1">Только тимейты</option><option value="2">Выключено</option></select>',
    'mp_roundtime' => '<input value="[mp_roundtime]" name="config[\'mp_roundtime\']">',
    'mp_startmoney' => '<input value="[mp_startmoney]" name="config[\'mp_startmoney\']">',
    'mp_timelimit' => '<input value="[mp_timelimit]" name="config[\'mp_timelimit\']">',
    'mp_tkpunish' => '<select name="config[\'mp_tkpunish\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'mp_winlimit' => '<input value="[mp_winlimit]" name="config[\'mp_winlimit\']">',
    'sv_allowdownload' => '<select name="config[\'sv_allowdownload\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'sv_allowupload' => '<select name="config[\'sv_allowupload\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'sv_alltalk' => '<select name="config[\'sv_alltalk\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'sv_gravity' => '<input value="[sv_gravity]" name="config[\'sv_gravity\']">',
    'sv_maxspeed' => '<input value="[sv_maxspeed]" name="config[\'sv_maxspeed\']">',
    'sv_maxunlag' => '<input value="[sv_maxunlag]" name="config[\'sv_maxunlag\']">',
    'pausable' => '<select name="config[\'pausable\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'sv_voiceenable' => '<select name="config[\'sv_voiceenable\']"><option value="0">Выключено</option><option value="1">Включено</option></select>',
    'mp_c4timer' => '<input value="[mp_c4timer]" name="config[\'mp_c4timer\']">',
    'mp_consistency' => '<select name="config[\'mp_consistency\']"><option value="0">Запрещено</option><option value="1">Разрешено</option></select>',
    'mp_buytime' => '<input value="[mp_buytime]" name="config[\'mp_buytime\']">',
    'sv_contact' => '<input value="[sv_contact]" name="config[\'sv_contact\']">',
    'sv_rcon_banpenalty' => '<input value="[sv_rcon_banpenalty]" name="config[\'sv_rcon_banpenalty\']">',
    'sv_rcon_maxfailures' => '<input value="[sv_rcon_maxfailures]" name="config[\'sv_rcon_maxfailures\']">',
    'sv_rcon_minfailures' => '<input value="[sv_rcon_minfailures]" name="config[\'sv_rcon_minfailures\']">',
    'sv_rcon_minfailuretime' => '<input value="[sv_rcon_minfailuretime]" name="config[\'sv_rcon_minfailuretime\']">',
    'sv_maxrate' => '<input value="[sv_maxrate]" name="config[\'sv_maxrate\']">',
    'sv_maxupdaterate' => '<input value="[sv_maxupdaterate]" name="config[\'sv_maxupdaterate\']">',
    'sv_minrate' => '<input value="[sv_minrate]" name="config[\'sv_minrate\']">',
    'sv_minupdaterate' => '<input value="[sv_minupdaterate]" name="config[\'sv_minupdaterate\']">',
    'mp_mapvoteratio' => '<input value="[mp_mapvoteratio]" name="config[\'mp_mapvoteratio\']">',
    'mp_forcechasecam' => '<select name="config[\'mp_forcechasecam\']"><option value="0">Можно наблюдать за всеми с любого ракурса</option><option value="1">Можно наблюдать только за игроками своей команды</option><option value="2">Наблюдение будет доступно лишь с места вашей смерти. Тоесть, камеру нельзя будет двигать</option></select>'
);
