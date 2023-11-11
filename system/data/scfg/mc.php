<?php
    if(!DEFINED('EGP'))
		exit(header('Refresh: 0; URL=http://'.$_SERVER['SERVER_NAME'].'/404'));

	$aScfg = array(
		'motd' => 'Название игрового сервера.',
		'server-name' => 'Название игрового сервера.',
		'level-name' => 'Название карты.',
		'rcon.password' => 'ароль для управления сервером через RCON команды.',
		'enable-rcon' => 'Разрешено управление RCON командами на сервере.',
		'allow-nether' => 'Разрешено игрокам путешествовать в Нижний мир (Nether).',
		'allow-flight' => 'Разрешено игрокам летать в режиме выживания, если установлен мод для полетов.',
		'white-list' => 'Белый список на сервере. Если на сервере включен "белый лист", то на нем могут играть только пользователи записанные в файле white-list.txt.',
		'spawn-animals' => 'Появление животных.',
		'spawn-monsters' => 'Появление монстров.',
		'online-mode' => 'Проверять наличие игрока в базе аккаунтов minecraft. При включении, игроки смогут играть только с лицензионного клиента игры.',
		'pvp' => 'Получение урона игрокам от атак других игроков на сервере. При выключении игроки не смогут наносить прямой урон один другому.',
		'difficulty' => 'Cложность игры на сервере..',
		'gamemode' => 'Режим игры на сервере.',
		'view-distance' => 'Дистанция обзора со стороны сервера. <u>Рекомендуемое значение 10</u>. Если наблюдаются сильные лаги можно уменьшить это значение.',
		'level-seed' => 'Входные данные (сид) для генератора уровня.',
		'level-type' => 'Опция генерации мира. <b>DEFAULT</b> - Стандартный, <b>FLAT</b> - Суперплоскость, <b>LARGEBIOMES</b> - Большие биомы, <b>DAYZ</b>, <b>BIOMESOP</b>.',
		'generate-structures' => 'Генерировать ли структуры (сокровищницы, крепости, деревни...).',
		'max-built-height' => 'Указывает максимальную высоту постройки на сервере.',
		'texture-pack' => '<u>Для серверов версии ниже 1.7.2</u>. Архив текстур, который сервер предложит загрузить игроку при соединении. В данном поле нужно указать имя zip-архива, находящегося в папке сервера.',
		'resource-pack' => '<u>Для серверов версии ниже 1.7.2</u>. Архив ресурсов, который сервер предложит загрузить игроку при соединении. В данном поле нужно указать имя zip-архива, находящегося в папке сервера.',
		'enable-command-block' => 'Командный блок.',
		'hardcore' => 'Включает на сервере режим Хардкор. После смерти - бан, переподключиться нельзя.',
		'announce-player-achievements' => 'Отправлять в чат сообщения о получении достижений.',
		'op-permission-level' => 'Позволяет изменять права операторов.'
				.'<li>1 - Операторы могут ломать / ставить блоки внутри радиуса защиты территории спауна.</li>'
				.'<li>2 - Операторы могут использовать команды /clear, /difficulty, /effect, /gamemode, /gamerule, /give, /tp, и могут изменять командные блоки.</li>'
				.'<li>3 - Операторы могут использовать команды /ban, /deop, /kick, и /op.</li>'
				.'<li>4 - Операторы могут использовать команду /stop.</li>'
	);

	$aScfg_form = array(
		'motd' => '<input value="[motd]" name="config[\'motd\']">',
		'server-name' => '<input value="[server-name]" name="config[\'server-name\']">',
		'level-name' => '<input value="[level-name]" name="config[\'level-name\']">',
		'rcon.password' => '<input value="[rcon.password]" name="config[\'rcon.password\']">',
		'view-distance' => '<input value="[view-distance]" name="config[\'view-distance\']">',
		'level-seed' => '<input value="[level-seed]" name="config[\'level-seed\']">',
		'level-type' => '<input value="[level-type]" name="config[\'level-type\']">',
		'texture-pack' => '<input value="[texture-pack]" name="config[\'texture-pack\']">',
		'resource-pack' => '<input value="[resource-pack]" name="config[\'resource-pack\']">',
		'enable-rcon' => '<select name="config[\'enable-rcon\']">'
						.'<option value="0">Запрещено</option>'
						.'<option value="1">Разрешено</option></select>',
		'generate-structures' => '<select name="config[\'generate-structures\']">'
						.'<option value="0">Не генерировать</option>'
						.'<option value="1">Генерировать</option></select>',
		'allow-nether' => '<select name="config[\'allow-nether\']">'
						.'<option value="0">Запрещено</option>'
						.'<option value="1">Разрешено</option></select>',
		'allow-flight' => '<select name="config[\'allow-flight\']">'
						.'<option value="0">Запрещено</option>'
						.'<option value="1">Разрешено</option></select>',
		'white-list' => '<select name="config[\'white-list\']">'
						.'<option value="0">Выключен</option>'
						.'<option value="1">Включен</option></select>',
		'hardcore' => '<select name="config[\'hardcore\']">'
						.'<option value="0">Выключен</option>'
						.'<option value="1">Включен</option></select>',
		'spawn-animals' => '<select name="config[\'spawn-animals\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'announce-player-achievements' => '<select name="config[\'announce-player-achievements\']">'
						.'<option value="0">Нет</option>'
						.'<option value="1">Да</option></select>',
		'spawn-monsters' => '<select name="config[\'spawn-monsters\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'online-mode' => '<select name="config[\'online-mode\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'pvp' => '<select name="config[\'pvp\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'enable-command-block' => '<select name="config[\'enable-command-block\']">'
						.'<option value="0">Выключено</option>'
						.'<option value="1">Включено</option></select>',
		'difficulty' => '<select name="config[\'difficulty\']">'
						.'<option value="3">Сложный</option>'
						.'<option value="1">Легкий</option>'
						.'<option value="2">Нормальный</option>'
						.'<option value="0">Мирный</option></select>',
		'gamemode' => '<select name="config[\'gamemode\']">'
						.'<option value="0">Survival (Выживание)</option>'
						.'<option value="1">Creative (Творческий)</option>'
						.'<option value="2">Adventure (Приключение)</option>'
						.'<option value="3">Hardcore (Хардкор)</option></select>',
		'max-built-height' => '<select name="config[\'max-built-height\']">'
						.'<option value="64">64</option>'
						.'<option value="80">80</option>'
						.'<option value="96">96</option>'
						.'<option value="112">112</option>'
						.'<option value="128">128</option>'
						.'<option value="144">144</option>'
						.'<option value="160">160</option>'
						.'<option value="176">176</option>'
						.'<option value="192">192</option>'
						.'<option value="208">208</option>'
						.'<option value="224">224</option>'
						.'<option value="240">240</option>'
						.'<option value="256">256</option></select>',
		'op-permission-level' => '<select name="config[\'op-permission-level\']">'
						.'<option value="1">1</option>'
						.'<option value="2">2</option>'
						.'<option value="3">3</option>'
						.'<option value="4">4</option></select>'
	);
?>