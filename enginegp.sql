SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Структура таблицы `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `ip` char(16) NOT NULL,
  `price` int(11) NOT NULL,
  `buy` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `address_buy`
--

CREATE TABLE `address_buy` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_crmp`
--

CREATE TABLE `admins_crmp` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_cs`
--

CREATE TABLE `admins_cs` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `value` varchar(50) NOT NULL,
  `passwd` char(32) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `type` char(3) NOT NULL,
  `time` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `info` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_cs2`
--

CREATE TABLE `admins_cs2` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `value` varchar(50) NOT NULL,
  `passwd` char(32) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_csgo`
--

CREATE TABLE `admins_csgo` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `value` varchar(50) NOT NULL,
  `passwd` char(32) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_css`
--

CREATE TABLE `admins_css` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `value` varchar(50) NOT NULL,
  `passwd` char(32) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_cssold`
--

CREATE TABLE `admins_cssold` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `value` varchar(50) NOT NULL,
  `passwd` char(32) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_mc`
--

CREATE TABLE `admins_mc` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_mta`
--

CREATE TABLE `admins_mta` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_rust`
--

CREATE TABLE `admins_rust` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `value` varchar(50) NOT NULL,
  `passwd` char(32) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `info` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_samp`
--

CREATE TABLE `admins_samp` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `api`
--

CREATE TABLE `api` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `key` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `auth`
--

CREATE TABLE `auth` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `ip` char(15) NOT NULL,
  `date` int(11) NOT NULL,
  `browser` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boost`
--

CREATE TABLE `boost` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `site` varchar(20) NOT NULL,
  `circles` int(11) NOT NULL,
  `money` float NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `boost_rating`
--

CREATE TABLE `boost_rating` (
  `id` int(11) NOT NULL,
  `boost` varchar(15) NOT NULL,
  `rating` int(11) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cashback`
--

CREATE TABLE `cashback` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `purse` varchar(13) NOT NULL,
  `money` float NOT NULL,
  `date` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `copy`
--

CREATE TABLE `copy` (
  `id` int(11) NOT NULL,
  `user` text DEFAULT NULL,
  `game` char(6) NOT NULL,
  `server` int(11) NOT NULL,
  `pack` varchar(100) NOT NULL,
  `name` char(32) NOT NULL,
  `info` varchar(100) NOT NULL,
  `plugins` text NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `crontab`
--

CREATE TABLE `crontab` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `task` char(7) NOT NULL DEFAULT '',
  `cron` text DEFAULT NULL,
  `week` text DEFAULT NULL,
  `time` char(20) NOT NULL DEFAULT '',
  `commands` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `firewall`
--

CREATE TABLE `firewall` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `sip` char(20) NOT NULL,
  `dest` char(27) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `graph`
--

CREATE TABLE `graph` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `key` char(32) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `graph_day`
--

CREATE TABLE `graph_day` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `online` int(11) NOT NULL,
  `cpu` int(11) NOT NULL,
  `ram` int(11) NOT NULL,
  `hdd` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `graph_hour`
--

CREATE TABLE `graph_hour` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `online` int(11) NOT NULL,
  `cpu` int(11) NOT NULL,
  `ram` int(11) NOT NULL,
  `hdd` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `help`
--

CREATE TABLE `help` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `service` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `close` int(11) NOT NULL,
  `notice` tinyint(1) NOT NULL DEFAULT 0,
  `notice_admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `help_dialogs`
--

CREATE TABLE `help_dialogs` (
  `id` int(11) NOT NULL,
  `help` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `text` varchar(1000) NOT NULL,
  `img` varchar(1000) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `help_upload`
--

CREATE TABLE `help_upload` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `name` varchar(36) NOT NULL,
  `time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` int(11) NOT NULL,
  `type` char(10) NOT NULL,
  `money` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `logs_sys`
--

CREATE TABLE `logs_sys` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `server` int(11) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `maps`
--

CREATE TABLE `maps` (
  `id` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `game` char(10) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `full_text` text NOT NULL,
  `tags` varchar(100) NOT NULL,
  `views` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `name`, `text`, `full_text`, `tags`, `views`, `date`) VALUES
(1, 'Спасибо за установку EngineGP', 'Благодарим за установку EngineGP и напоминаем, что может данная панель управления.\r\n\r\n&lt;b&gt;1. Управление серверами игр:&lt;/b&gt;\r\n- Counter-Strike: 1.6\r\n- Counter-Strike: Source v34\r\n- Counter-Strike: Source\r\n- Counter-Strike: Global Offensive\r\n...\n', 'Благодарим за установку EngineGP и напоминаем, что может данная панель управления.\n\n&lt;b&gt;1. Управление серверами игр:&lt;/b&gt;\n- Counter-Strike: 1.6\n- Counter-Strike: Source v34\n- Counter-Strike: Source\n- Counter-Strike: Global Offensive\n- Counter-Strike: 2\n- San Andreas Multiplayer\n- Criminal Russia Multiplayer\n- Multi Theft Auto\n- Minecraft Java Edition\n- RUST\n\n&lt;b&gt;2. Биллинг пользователей&lt;/b&gt;\n- Предоставление аренды игрового сервера\n- Пополнение через платежные агрегаторы FreeKassa, YooKassa\n- Реферальная программа с выводом на банковские карты, электронные кошельки и на баланс сайта\n- Система тикетов для взаимодействия с пользователями\n\n&lt;b&gt;3. Возможности игровых серверов&lt;/b&gt;\n- Включение, выключение, перезагрузка и обновление через SteamCMD или локально\n- Онлайн консоль\n- Онлайн файловый менеджер\n- Доступ к файлам сервера по FTP\n- FireWall с использованием iptables\n- FastDL для Counter-Strike\n- Управление картами для Counter-Strike\n- Плагины/модификации\n- Выдача прав на игровой сервер для других пользователей\n- Графики нагрузки\n- Баннеры для сайтов и форумов\n- Выбор версии Java для Minecraft Java Edition\n- Boost серверов Counter-Strike: 1.6\n\n&lt;b&gt;4. Другие возможности&lt;/b&gt;\n- Управление новостями на сайте\n- Создание кастомных страниц сайта\n- Логи операций\n- Массовая рассылка email почты\n- Википедия\n- Мониторинг игровых серверов\n', 'EngineGP', 0, UNIX_TIMESTAMP());

-- --------------------------------------------------------

--
-- Структура таблицы `notice`
--

CREATE TABLE `notice` (
  `id` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `server` int(11) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `color` char(10) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `rights` text NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `file` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `panel`
--

CREATE TABLE `panel` (
  `address` char(21) NOT NULL,
  `passwd` char(32) NOT NULL,
  `path` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `panel`
--

INSERT INTO `panel` (`address`, `passwd`, `path`) VALUES
('IPADDR:22', 'ROOTPASSWORD', '/var/www/enginegp/');

-- --------------------------------------------------------

--
-- Структура таблицы `plugins`
--

CREATE TABLE `plugins` (
  `id` int(11) NOT NULL,
  `cat` int(11) NOT NULL,
  `game` char(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `desc` text DEFAULT NULL,
  `info` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `cfg` int(11) NOT NULL,
  `upd` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `incompatible` varchar(100) NOT NULL DEFAULT '',
  `choice` varchar(100) NOT NULL DEFAULT '',
  `required` varchar(100) NOT NULL DEFAULT '',
  `packs` varchar(100) NOT NULL DEFAULT 'all',
  `price` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_buy`
--

CREATE TABLE `plugins_buy` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `server` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_category`
--

CREATE TABLE `plugins_category` (
  `id` int(11) NOT NULL,
  `game` char(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_clear`
--

CREATE TABLE `plugins_clear` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `file` varchar(100) NOT NULL,
  `regex` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_config`
--

CREATE TABLE `plugins_config` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `file` varchar(100) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_delete`
--

CREATE TABLE `plugins_delete` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `file` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_delete_ins`
--

CREATE TABLE `plugins_delete_ins` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `install` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_install`
--

CREATE TABLE `plugins_install` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `upd` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_update`
--

CREATE TABLE `plugins_update` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `desc` text DEFAULT NULL,
  `info` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `cfg` int(11) NOT NULL,
  `upd` int(11) NOT NULL,
  `incompatible` varchar(100) NOT NULL DEFAULT '',
  `choice` varchar(100) NOT NULL DEFAULT '',
  `required` varchar(100) NOT NULL DEFAULT '',
  `packs` varchar(100) NOT NULL DEFAULT '',
  `price` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_write`
--

CREATE TABLE `plugins_write` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL,
  `top` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_write_del`
--

CREATE TABLE `plugins_write_del` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `text` varchar(100) NOT NULL,
  `file` varchar(100) NOT NULL,
  `top` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `privileges`
--

CREATE TABLE `privileges` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `privileges_buy`
--

CREATE TABLE `privileges_buy` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `text` text NOT NULL,
  `sql` text NOT NULL,
  `price` float NOT NULL,
  `key` varchar(32) NOT NULL,
  `date` int(11) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `privileges_list`
--

CREATE TABLE `privileges_list` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL DEFAULT 0,
  `data` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promo`
--

CREATE TABLE `promo` (
  `id` int(11) NOT NULL,
  `cod` char(20) NOT NULL,
  `value` char(4) NOT NULL,
  `discount` tinyint(1) NOT NULL,
  `data` text NOT NULL,
  `hits` int(11) NOT NULL,
  `use` int(11) NOT NULL,
  `extend` tinyint(1) NOT NULL,
  `tarif` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_use`
--

CREATE TABLE `promo_use` (
  `id` int(11) NOT NULL,
  `promo` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `recovery`
--

CREATE TABLE `recovery` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `mail` char(50) NOT NULL,
  `key` char(32) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `security`
--

CREATE TABLE `security` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `address` char(20) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0,
  `unit` int(11) NOT NULL DEFAULT 0,
  `tarif` int(11) NOT NULL DEFAULT 0,
  `user` int(11) NOT NULL DEFAULT 0,
  `address` char(21) NOT NULL DEFAULT '',
  `port` int(11) NOT NULL DEFAULT 0,
  `port_query` int(11) NOT NULL DEFAULT 0,
  `port_rcon` int(11) NOT NULL DEFAULT 0,
  `game` char(6) NOT NULL DEFAULT '',
  `slots` int(11) NOT NULL DEFAULT 0,
  `slots_start` int(11) NOT NULL DEFAULT 0,
  `online` int(11) NOT NULL DEFAULT 0,
  `players` text DEFAULT NULL,
  `status` char(10) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `pack` varchar(50) NOT NULL DEFAULT '',
  `plugins_use` tinyint(1) NOT NULL DEFAULT 0,
  `console_use` tinyint(1) NOT NULL DEFAULT 0,
  `stats_use` tinyint(1) NOT NULL DEFAULT 0,
  `copy_use` tinyint(1) NOT NULL DEFAULT 0,
  `web_use` tinyint(1) NOT NULL DEFAULT 0,
  `ftp_use` tinyint(1) NOT NULL DEFAULT 0,
  `ftp` tinyint(1) NOT NULL DEFAULT 0,
  `ftp_root` tinyint(1) NOT NULL DEFAULT 0,
  `ftp_passwd` char(20) NOT NULL DEFAULT '',
  `ftp_on` tinyint(1) NOT NULL DEFAULT 0,
  `fps` int(11) NOT NULL DEFAULT 0,
  `tickrate` int(11) NOT NULL DEFAULT 0,
  `ram` int(11) NOT NULL DEFAULT 0,
  `ram_use` int(11) NOT NULL DEFAULT 0,
  `map` varchar(100) NOT NULL DEFAULT '',
  `map_start` varchar(100) NOT NULL DEFAULT '',
  `vac` tinyint(1) NOT NULL DEFAULT 0,
  `fastdl` int(11) NOT NULL DEFAULT 0,
  `pingboost` int(11) NOT NULL DEFAULT 0,
  `cpu` int(11) NOT NULL DEFAULT 0,
  `cpu_use` int(11) NOT NULL DEFAULT 0,
  `hdd` int(11) NOT NULL DEFAULT 0,
  `hdd_use` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL DEFAULT 0,
  `overdue` int(11) NOT NULL DEFAULT 0,
  `block` int(11) NOT NULL DEFAULT 0,
  `test` tinyint(1) NOT NULL DEFAULT 0,
  `stop` tinyint(1) NOT NULL DEFAULT 0,
  `autostop` tinyint(1) NOT NULL DEFAULT 0,
  `time_start` int(11) NOT NULL DEFAULT 0,
  `reinstall` int(11) NOT NULL DEFAULT 0,
  `update` int(11) NOT NULL DEFAULT 0,
  `benefit` int(11) NOT NULL DEFAULT 0,
  `autorestart` tinyint(1) NOT NULL DEFAULT 0,
  `sms` tinyint(1) NOT NULL DEFAULT 0,
  `mail` tinyint(1) NOT NULL DEFAULT 0,
  `ddos` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `mail` char(50) NOT NULL,
  `key` char(32) NOT NULL,
  `data` text NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tarifs`
--

CREATE TABLE `tarifs` (
  `id` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `game` char(8) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slots_min` int(11) NOT NULL,
  `slots_max` int(11) NOT NULL,
  `port_min` int(11) NOT NULL,
  `port_max` int(11) NOT NULL,
  `hostname` varchar(100) NOT NULL,
  `packs` text NOT NULL,
  `path` varchar(100) NOT NULL,
  `install` varchar(100) NOT NULL,
  `update` varchar(100) NOT NULL,
  `fps` varchar(100) NOT NULL,
  `tickrate` varchar(100) NOT NULL,
  `cpu` int(11) NOT NULL,
  `ram` int(11) NOT NULL,
  `time` varchar(100) NOT NULL,
  `timext` varchar(100) NOT NULL,
  `test` int(11) NOT NULL,
  `tests` int(11) NOT NULL,
  `discount` tinyint(1) NOT NULL,
  `map` varchar(50) NOT NULL,
  `ftp` tinyint(1) NOT NULL DEFAULT 0,
  `plugins` tinyint(1) NOT NULL DEFAULT 0,
  `console` tinyint(1) NOT NULL DEFAULT 0,
  `stats` tinyint(1) NOT NULL DEFAULT 0,
  `copy` tinyint(1) NOT NULL DEFAULT 0,
  `web` tinyint(1) NOT NULL DEFAULT 0,
  `plugins_install` text NOT NULL,
  `hdd` int(11) NOT NULL,
  `autostop` tinyint(1) NOT NULL,
  `price` text DEFAULT NULL,
  `ip` text DEFAULT NULL,
  `show` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `game` char(6) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` char(21) NOT NULL,
  `passwd` char(32) NOT NULL,
  `sql_login` char(20) NOT NULL,
  `sql_passwd` char(32) NOT NULL,
  `sql_port` int(11) NOT NULL,
  `sql_ftp` char(20) NOT NULL,
  `cs` tinyint(1) NOT NULL DEFAULT 0,
  `cssold` tinyint(1) NOT NULL DEFAULT 0,
  `rust` tinyint(1) NOT NULL DEFAULT 0,
  `css` tinyint(1) NOT NULL DEFAULT 0,
  `csgo` tinyint(1) NOT NULL DEFAULT 0,
  `cs2` tinyint(1) NOT NULL DEFAULT 0,
  `samp` tinyint(1) NOT NULL DEFAULT 0,
  `crmp` tinyint(1) NOT NULL DEFAULT 0,
  `mta` tinyint(1) NOT NULL DEFAULT 0,
  `mc` tinyint(1) NOT NULL DEFAULT 0,
  `ram` int(11) NOT NULL,
  `test` int(11) NOT NULL,
  `show` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  `domain` varchar(40) NOT NULL DEFAULT '',
  `ddos` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` char(15) NOT NULL,
  `passwd` char(255) NOT NULL,
  `name` char(32) NOT NULL,
  `lastname` char(32) NOT NULL,
  `patronymic` char(32) NOT NULL,
  `mail` char(50) NOT NULL,
  `new_mail` char(50) NOT NULL DEFAULT '',
  `confirm_mail` char(32) NOT NULL DEFAULT '',
  `phone` char(12) NOT NULL,
  `confirm_phone` int(6) NOT NULL DEFAULT 0,
  `contacts` varchar(100) NOT NULL,
  `balance` float NOT NULL,
  `wmr` char(13) NOT NULL DEFAULT '',
  `group` char(7) NOT NULL,
  `support_info` varchar(50) NOT NULL DEFAULT '',
  `level` int(11) NOT NULL DEFAULT 0,
  `ip` char(16) NOT NULL DEFAULT '',
  `browser` char(20) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL,
  `part` int(11) NOT NULL DEFAULT 0,
  `part_money` float NOT NULL DEFAULT 0,
  `security_ip` tinyint(1) NOT NULL DEFAULT 0,
  `security_code` tinyint(1) NOT NULL DEFAULT 0,
  `notice_help` tinyint(1) NOT NULL DEFAULT 0,
  `notice_news` tinyint(1) NOT NULL DEFAULT 1,
  `help` tinyint(1) NOT NULL DEFAULT 0,
  `rental` varchar(4) NOT NULL DEFAULT '0',
  `extend` varchar(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `passwd`, `name`, `lastname`, `patronymic`, `mail`, `new_mail`, `confirm_mail`, `phone`, `confirm_phone`, `contacts`, `balance`, `wmr`, `group`, `support_info`, `level`, `ip`, `browser`, `time`, `date`, `part`, `part_money`, `security_ip`, `security_code`, `notice_help`, `notice_news`, `help`, `rental`, `extend`) VALUES
(1, 'admin', 'ENGINEGPHASH', 'Имя', 'Фамилия', 'Отчество', 'admin@example.com', '', '', '', 0, '', 10000, '', 'admin', '', 0, '127.0.0.1', 'Google Chrome', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 0, 0, 1, 0, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `web`
--

CREATE TABLE `web` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL,
  `desing` varchar(32) NOT NULL DEFAULT '',
  `server` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `domain` varchar(40) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `config` text NOT NULL,
  `login` varchar(32) NOT NULL DEFAULT '',
  `update` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wiki`
--

CREATE TABLE `wiki` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cat` int(11) NOT NULL,
  `tags` varchar(100) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wiki_answer`
--

CREATE TABLE `wiki_answer` (
  `wiki` int(11) NOT NULL,
  `cat` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `wiki_category`
--

CREATE TABLE `wiki_category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `address_buy`
--
ALTER TABLE `address_buy`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_crmp`
--
ALTER TABLE `admins_crmp`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_cs`
--
ALTER TABLE `admins_cs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_cs2`
--
ALTER TABLE `admins_cs2`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_csgo`
--
ALTER TABLE `admins_csgo`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_css`
--
ALTER TABLE `admins_css`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_cssold`
--
ALTER TABLE `admins_cssold`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_mc`
--
ALTER TABLE `admins_mc`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_mta`
--
ALTER TABLE `admins_mta`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_rust`
--
ALTER TABLE `admins_rust`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `admins_samp`
--
ALTER TABLE `admins_samp`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `api`
--
ALTER TABLE `api`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boost`
--
ALTER TABLE `boost`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `boost_rating`
--
ALTER TABLE `boost_rating`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cashback`
--
ALTER TABLE `cashback`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `copy`
--
ALTER TABLE `copy`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `crontab`
--
ALTER TABLE `crontab`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `firewall`
--
ALTER TABLE `firewall`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `graph`
--
ALTER TABLE `graph`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `graph_day`
--
ALTER TABLE `graph_day`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `graph_hour`
--
ALTER TABLE `graph_hour`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `help`
--
ALTER TABLE `help`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `help_dialogs`
--
ALTER TABLE `help_dialogs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `help_upload`
--
ALTER TABLE `help_upload`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `logs_sys`
--
ALTER TABLE `logs_sys`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `maps`
--
ALTER TABLE `maps`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `panel`
--
ALTER TABLE `panel`
  ADD PRIMARY KEY (`address`);

--
-- Индексы таблицы `plugins`
--
ALTER TABLE `plugins`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_buy`
--
ALTER TABLE `plugins_buy`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_category`
--
ALTER TABLE `plugins_category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_clear`
--
ALTER TABLE `plugins_clear`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_config`
--
ALTER TABLE `plugins_config`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_delete`
--
ALTER TABLE `plugins_delete`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_delete_ins`
--
ALTER TABLE `plugins_delete_ins`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_install`
--
ALTER TABLE `plugins_install`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_update`
--
ALTER TABLE `plugins_update`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_write`
--
ALTER TABLE `plugins_write`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `plugins_write_del`
--
ALTER TABLE `plugins_write_del`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `privileges_buy`
--
ALTER TABLE `privileges_buy`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `privileges_list`
--
ALTER TABLE `privileges_list`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `promo_use`
--
ALTER TABLE `promo_use`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `recovery`
--
ALTER TABLE `recovery`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `security`
--
ALTER TABLE `security`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tarifs`
--
ALTER TABLE `tarifs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `web`
--
ALTER TABLE `web`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `wiki`
--
ALTER TABLE `wiki`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `wiki_answer`
--
ALTER TABLE `wiki_answer`
  ADD PRIMARY KEY (`wiki`);

--
-- Индексы таблицы `wiki_category`
--
ALTER TABLE `wiki_category`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `address_buy`
--
ALTER TABLE `address_buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_crmp`
--
ALTER TABLE `admins_crmp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_cs`
--
ALTER TABLE `admins_cs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_cs2`
--
ALTER TABLE `admins_cs2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_csgo`
--
ALTER TABLE `admins_csgo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_css`
--
ALTER TABLE `admins_css`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_cssold`
--
ALTER TABLE `admins_cssold`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_mc`
--
ALTER TABLE `admins_mc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_mta`
--
ALTER TABLE `admins_mta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_rust`
--
ALTER TABLE `admins_rust`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `admins_samp`
--
ALTER TABLE `admins_samp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `api`
--
ALTER TABLE `api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `auth`
--
ALTER TABLE `auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boost`
--
ALTER TABLE `boost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `boost_rating`
--
ALTER TABLE `boost_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `cashback`
--
ALTER TABLE `cashback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `copy`
--
ALTER TABLE `copy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `crontab`
--
ALTER TABLE `crontab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `firewall`
--
ALTER TABLE `firewall`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `graph`
--
ALTER TABLE `graph`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `graph_day`
--
ALTER TABLE `graph_day`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `graph_hour`
--
ALTER TABLE `graph_hour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `help`
--
ALTER TABLE `help`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `help_dialogs`
--
ALTER TABLE `help_dialogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `help_upload`
--
ALTER TABLE `help_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `logs_sys`
--
ALTER TABLE `logs_sys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `maps`
--
ALTER TABLE `maps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `notice`
--
ALTER TABLE `notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins`
--
ALTER TABLE `plugins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_buy`
--
ALTER TABLE `plugins_buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_category`
--
ALTER TABLE `plugins_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_clear`
--
ALTER TABLE `plugins_clear`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_config`
--
ALTER TABLE `plugins_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_delete`
--
ALTER TABLE `plugins_delete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_delete_ins`
--
ALTER TABLE `plugins_delete_ins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_install`
--
ALTER TABLE `plugins_install`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_update`
--
ALTER TABLE `plugins_update`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_write`
--
ALTER TABLE `plugins_write`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `plugins_write_del`
--
ALTER TABLE `plugins_write_del`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `privileges`
--
ALTER TABLE `privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `privileges_buy`
--
ALTER TABLE `privileges_buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `privileges_list`
--
ALTER TABLE `privileges_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `promo_use`
--
ALTER TABLE `promo_use`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `recovery`
--
ALTER TABLE `recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `security`
--
ALTER TABLE `security`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tarifs`
--
ALTER TABLE `tarifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `web`
--
ALTER TABLE `web`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wiki`
--
ALTER TABLE `wiki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wiki_answer`
--
ALTER TABLE `wiki_answer`
  MODIFY `wiki` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `wiki_category`
--
ALTER TABLE `wiki_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
