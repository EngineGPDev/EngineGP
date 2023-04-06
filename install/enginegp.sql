-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Июн 28 2020 г., 16:19
-- Версия сервера: 5.7.30
-- Версия PHP: 5.6.40-29+0~20200514.35+debian9~1.gbpcc49a4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `enginegp`
--

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `address_buy`
--

CREATE TABLE `address_buy` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_crmp`
--

CREATE TABLE `admins_crmp` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_mc`
--

CREATE TABLE `admins_mc` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_mta`
--

CREATE TABLE `admins_mta` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `admins_samp`
--

CREATE TABLE `admins_samp` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `api`
--

CREATE TABLE `api` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `key` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `boost_rating`
--

CREATE TABLE `boost_rating` (
  `id` int(11) NOT NULL,
  `boost` varchar(15) NOT NULL,
  `rating` int(11) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `msg` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control`
--

CREATE TABLE `control` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `address` varchar(15) NOT NULL,
  `passwd` varchar(32) NOT NULL,
  `sql_login` char(20) NOT NULL DEFAULT 'root',
  `sql_passwd` char(32) NOT NULL DEFAULT '',
  `sql_port` int(11) NOT NULL DEFAULT '3306',
  `sql_ftp` char(20) NOT NULL DEFAULT 'ftp',
  `time` int(11) NOT NULL,
  `overdue` int(11) NOT NULL DEFAULT '0',
  `block` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT '',
  `install` tinyint(1) NOT NULL DEFAULT '0',
  `fcpu` tinyint(1) NOT NULL DEFAULT '0',
  `limit` int(11) NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `ram` int(11) NOT NULL DEFAULT '0',
  `hdd` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_admins_cs`
--

CREATE TABLE `control_admins_cs` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_admins_csgo`
--

CREATE TABLE `control_admins_csgo` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_admins_css`
--

CREATE TABLE `control_admins_css` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_admins_cssold`
--

CREATE TABLE `control_admins_cssold` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_boost`
--

CREATE TABLE `control_boost` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `site` varchar(20) NOT NULL,
  `circles` int(11) NOT NULL,
  `money` float NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_copy`
--

CREATE TABLE `control_copy` (
  `id` int(11) NOT NULL,
  `user` text,
  `game` char(6) NOT NULL,
  `server` int(11) NOT NULL,
  `pack` varchar(100) NOT NULL,
  `name` char(32) NOT NULL,
  `info` varchar(100) NOT NULL,
  `plugins` text NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_crontab`
--

CREATE TABLE `control_crontab` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `task` char(7) NOT NULL DEFAULT '',
  `cron` text,
  `week` text,
  `time` char(20) NOT NULL DEFAULT '',
  `commands` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_firewall`
--

CREATE TABLE `control_firewall` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `sip` char(20) NOT NULL,
  `dest` char(27) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_plugins_buy`
--

CREATE TABLE `control_plugins_buy` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `server` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_plugins_install`
--

CREATE TABLE `control_plugins_install` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `upd` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `control_servers`
--

CREATE TABLE `control_servers` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `unit` int(11) NOT NULL,
  `address` char(21) NOT NULL,
  `game` char(6) NOT NULL,
  `slots` int(11) NOT NULL,
  `online` int(11) NOT NULL DEFAULT '0',
  `players` text,
  `status` char(10) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'Новый сервер',
  `pack` varchar(10) NOT NULL DEFAULT '',
  `fps` int(11) NOT NULL DEFAULT '0',
  `tickrate` int(11) NOT NULL DEFAULT '0',
  `map` varchar(100) NOT NULL DEFAULT '',
  `map_start` varchar(100) NOT NULL DEFAULT '',
  `time_start` int(11) NOT NULL DEFAULT '0',
  `ram_use` int(11) NOT NULL DEFAULT '0',
  `cpu_use` int(11) NOT NULL DEFAULT '0',
  `hdd_use` int(11) NOT NULL DEFAULT '0',
  `core_use` int(11) NOT NULL DEFAULT '0',
  `autorestart` int(11) NOT NULL DEFAULT '0',
  `pingboost` int(11) NOT NULL DEFAULT '0',
  `vac` int(11) NOT NULL DEFAULT '1',
  `fastdl` int(11) NOT NULL DEFAULT '0',
  `core_fix` int(11) NOT NULL DEFAULT '0',
  `stop` tinyint(1) NOT NULL DEFAULT '0',
  `ftp` int(11) NOT NULL DEFAULT '0',
  `ftp_passwd` char(20) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `copy`
--

CREATE TABLE `copy` (
  `id` int(11) NOT NULL,
  `user` text,
  `game` char(6) NOT NULL,
  `server` int(11) NOT NULL,
  `pack` varchar(100) NOT NULL,
  `name` char(32) NOT NULL,
  `info` varchar(100) NOT NULL,
  `plugins` text NOT NULL,
  `date` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `crontab`
--

CREATE TABLE `crontab` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `task` char(7) NOT NULL DEFAULT '',
  `cron` text,
  `week` text,
  `time` char(20) NOT NULL DEFAULT '',
  `commands` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `graph`
--

CREATE TABLE `graph` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `key` char(32) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `notice` tinyint(1) NOT NULL DEFAULT '0',
  `notice_admin` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `job` varchar(150) NOT NULL,
  `desc` text NOT NULL,
  `status` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `jobs_app`
--

CREATE TABLE `jobs_app` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `text` text NOT NULL,
  `contact` varchar(100) NOT NULL,
  `job` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `logs_sys`
--

CREATE TABLE `logs_sys` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `server` int(11) NOT NULL DEFAULT '0',
  `control` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `maps`
--

CREATE TABLE `maps` (
  `id` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `game` char(10) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `name`, `text`, `full_text`, `tags`, `views`, `date`) VALUES
(1, 'Мы открылись!', 'Мы с удовольствием сообщаем, что сегодня всем поклонникам он-лайн игр стал доступен наш мультифункциональный сервис по аренде игровых серверов.\r\n\r\nВ результате профессиональной работы и тщательной технической подготовки был получен производительный и удобный для пользователей IT-продукт, который в самое ближайшее время составит серьёзную конкуренцию всем существующим в этой рыночной нише проектам.\r\n\r\nНаша команда в процессе разработки сервиса старалась учитывать все слабые и сильные стороны аналогичных предложений. Во главе угла ставились надёжное быстродействие хостинга и максимально доступные цены для пользователей. Предлагая вам лучшие условия аренды игровых серверов нового поколения, мы рассчитываем на долгосрочное сотрудничество и каждого заказчика рассматриваем как перспективного партнёра.\r\n\r\nС сегодняшнего дня великолепный мир захватывающих интерактивных игр стал более доступным и технически совершенным, обеспечивая новый уровень эмоциональной насыщенности не прерывающимся ни на минуту компьютерным баталиям.\r\n', 'Мы с удовольствием сообщаем, что сегодня всем поклонникам он-лайн игр стал доступен наш мультифункциональный сервис по аренде игровых серверов.\r\n\r\nВ результате профессиональной работы и тщательной технической подготовки был получен производительный и удобный для пользователей IT-продукт, который в самое ближайшее время составит серьёзную конкуренцию всем существующим в этой рыночной нише проектам.\r\n\r\nНаша команда в процессе разработки сервиса старалась учитывать все слабые и сильные стороны аналогичных предложений. Во главе угла ставились надёжное быстродействие хостинга и максимально доступные цены для пользователей. Предлагая вам лучшие условия аренды игровых серверов нового поколения, мы рассчитываем на долгосрочное сотрудничество и каждого заказчика рассматриваем как перспективного партнёра.\r\n\r\nС сегодняшнего дня великолепный мир захватывающих интерактивных игр стал более доступным и технически совершенным, обеспечивая новый уровень эмоциональной насыщенности не прерывающимся ни на минуту компьютерным баталиям.\r\n', '', 0, 1577869200);

-- --------------------------------------------------------

--
-- Структура таблицы `notice`
--

CREATE TABLE `notice` (
  `id` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `server` int(11) NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `color` char(10) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `file` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `panel`
--

CREATE TABLE `panel` (
  `address` char(21) NOT NULL,
  `passwd` char(32) NOT NULL,
  `path` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `panel`
--

INSERT INTO `panel` (`address`, `passwd`, `path`) VALUES
('IPADDR:22', 'ROOTPASSWORD', '/var/enginegp/');

-- --------------------------------------------------------

--
-- Структура таблицы `plugins`
--

CREATE TABLE `plugins` (
  `id` int(11) NOT NULL,
  `cat` int(11) NOT NULL,
  `game` char(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `desc` text,
  `info` text,
  `images` text,
  `status` int(11) NOT NULL,
  `cfg` int(11) NOT NULL,
  `upd` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `incompatible` varchar(100) NOT NULL DEFAULT '',
  `choice` varchar(100) NOT NULL DEFAULT '',
  `required` varchar(100) NOT NULL DEFAULT '',
  `packs` varchar(100) NOT NULL DEFAULT 'all',
  `price` float NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_category`
--

CREATE TABLE `plugins_category` (
  `id` int(11) NOT NULL,
  `game` char(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_delete`
--

CREATE TABLE `plugins_delete` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `file` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_delete_ins`
--

CREATE TABLE `plugins_delete_ins` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `update` int(11) NOT NULL,
  `install` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_install`
--

CREATE TABLE `plugins_install` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `upd` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `plugins_update`
--

CREATE TABLE `plugins_update` (
  `id` int(11) NOT NULL,
  `plugin` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `desc` text,
  `info` text,
  `images` text,
  `status` int(11) NOT NULL,
  `cfg` int(11) NOT NULL,
  `upd` int(11) NOT NULL,
  `incompatible` varchar(100) NOT NULL DEFAULT '',
  `choice` varchar(100) NOT NULL DEFAULT '',
  `required` varchar(100) NOT NULL DEFAULT '',
  `packs` varchar(100) NOT NULL DEFAULT '',
  `price` float NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `privileges`
--

CREATE TABLE `privileges` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `privileges_list`
--

CREATE TABLE `privileges_list` (
  `id` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `flags` varchar(50) NOT NULL,
  `immunity` int(11) NOT NULL DEFAULT '0',
  `data` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `promo_use`
--

CREATE TABLE `promo_use` (
  `id` int(11) NOT NULL,
  `promo` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `security`
--

CREATE TABLE `security` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `address` char(20) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `unit` int(11) NOT NULL DEFAULT '0',
  `tarif` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `address` char(21) NOT NULL DEFAULT '',
  `port` int(11) NOT NULL DEFAULT '0',
  `game` char(6) NOT NULL DEFAULT '',
  `slots` int(11) NOT NULL DEFAULT '0',
  `slots_start` int(11) NOT NULL DEFAULT '0',
  `online` int(11) NOT NULL DEFAULT '0',
  `players` text,
  `status` char(10) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `pack` varchar(50) NOT NULL DEFAULT '',
  `plugins_use` tinyint(1) NOT NULL DEFAULT '0',
  `console_use` tinyint(1) NOT NULL DEFAULT '0',
  `stats_use` tinyint(1) NOT NULL DEFAULT '0',
  `copy_use` tinyint(1) NOT NULL DEFAULT '0',
  `web_use` tinyint(1) NOT NULL DEFAULT '0',
  `ftp_use` tinyint(1) NOT NULL DEFAULT '0',
  `ftp` tinyint(1) NOT NULL DEFAULT '0',
  `ftp_root` tinyint(1) NOT NULL DEFAULT '0',
  `ftp_passwd` char(20) NOT NULL DEFAULT '',
  `ftp_on` tinyint(1) NOT NULL DEFAULT '0',
  `fps` int(11) NOT NULL DEFAULT '0',
  `tickrate` int(11) NOT NULL DEFAULT '0',
  `ram` int(11) NOT NULL DEFAULT '0',
  `ram_use` int(11) NOT NULL DEFAULT '0',
  `ram_use_max` int(11) NOT NULL DEFAULT '0',
  `ram_fix` tinyint(1) NOT NULL DEFAULT '0',
  `map` varchar(100) NOT NULL DEFAULT '',
  `map_start` varchar(100) NOT NULL DEFAULT '',
  `vac` tinyint(1) NOT NULL DEFAULT '0',
  `fastdl` int(11) NOT NULL DEFAULT '0',
  `pingboost` int(11) NOT NULL DEFAULT '0',
  `cpu` int(11) NOT NULL DEFAULT '0',
  `cpu_use` int(11) NOT NULL DEFAULT '0',
  `cpu_use_max` int(11) NOT NULL DEFAULT '0',
  `core_fix` int(11) NOT NULL DEFAULT '0',
  `core_fix_one` tinyint(1) NOT NULL DEFAULT '0',
  `core_use` int(11) NOT NULL DEFAULT '0',
  `hdd` int(11) NOT NULL DEFAULT '0',
  `hdd_use` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `overdue` int(11) NOT NULL DEFAULT '0',
  `block` int(11) NOT NULL DEFAULT '0',
  `test` tinyint(1) NOT NULL DEFAULT '0',
  `stop` tinyint(1) NOT NULL DEFAULT '0',
  `autostop` tinyint(1) NOT NULL DEFAULT '0',
  `time_start` int(11) NOT NULL DEFAULT '0',
  `reinstall` int(11) NOT NULL DEFAULT '0',
  `update` int(11) NOT NULL DEFAULT '0',
  `benefit` int(11) NOT NULL DEFAULT '0',
  `autorestart` tinyint(1) NOT NULL DEFAULT '0',
  `sms` tinyint(1) NOT NULL DEFAULT '0',
  `mail` tinyint(1) NOT NULL DEFAULT '0',
  `ddos` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `ram` varchar(100) NOT NULL,
  `param_fix` tinyint(1) NOT NULL DEFAULT '0',
  `time` varchar(100) NOT NULL,
  `timext` varchar(100) NOT NULL,
  `test` int(11) NOT NULL,
  `tests` int(11) NOT NULL,
  `discount` tinyint(1) NOT NULL,
  `map` varchar(50) NOT NULL,
  `ftp` tinyint(1) NOT NULL DEFAULT '0',
  `plugins` tinyint(1) NOT NULL DEFAULT '0',
  `console` tinyint(1) NOT NULL DEFAULT '0',
  `stats` tinyint(1) NOT NULL DEFAULT '0',
  `copy` tinyint(1) NOT NULL DEFAULT '0',
  `web` tinyint(1) NOT NULL DEFAULT '0',
  `plugins_install` text NOT NULL,
  `hdd` int(11) NOT NULL,
  `autostop` tinyint(1) NOT NULL,
  `price` text,
  `core_fix` varchar(100) NOT NULL DEFAULT '',
  `ip` text,
  `show` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tarifs`
--

INSERT INTO `tarifs` (`id`, `unit`, `game`, `name`, `slots_min`, `slots_max`, `port_min`, `port_max`, `hostname`, `packs`, `path`, `install`, `update`, `fps`, `tickrate`, `ram`, `param_fix`, `time`, `timext`, `test`, `tests`, `discount`, `map`, `ftp`, `plugins`, `console`, `stats`, `copy`, `web`, `plugins_install`, `hdd`, `autostop`, `price`, `core_fix`, `ip`, `show`, `sort`) VALUES
(1, 1, 'cs', 'Обычный', 10, 32, 27016, 27116, 'Новый игровой сервер', 'eyJzdGVhbSI6IlNURUFNIFtcdTA0MjdcdTA0MzhcdTA0NDFcdTA0NDJcdTA0NGJcdTA0MzkgXHUwNDQxXHUwNDM1XHUwNDQwXHUwNDMyXHUwNDM1XHUwNDQwXSIsInJlaGxkcyI6IlJlSExEUyAzLjcuMC54eHgtZGV2IiwiODMwOCI6IkJVSUxEIDgzMDgiLCI4MTk2IjoiQlVJTEQgODE5NiIsIjc4ODIiOiJCVUlMRCA3ODgyIiwiNzU1OSI6IkJVSUxEIDc1NTkiLCI2MTUzIjoiQlVJTEQgNjE1MyIsIjU3ODciOiJCVUlMRCA1Nzg3In0=', '/path/cs/', '/servers/cs/', '/path/update/cs/', '500:1000', '66:100', '512', 0, '15:30:90:180', '7:30:90:180', 0, 0, 1, 'de_dust2', 1, 0, 1, 0, 1, 0, '', 5000, 0, '25:35', '', '', 1, 1),
(2, 1, 'cssold', 'Обычный', 10, 64, 27117, 27217, 'Новый игровой сервер', 'eyJzdGVhbSI6IlN0ZWFtIFtcdTA0MjdcdTA0MzhcdTA0NDFcdTA0NDJcdTA0NGJcdTA0MzkgXHUwNDQxXHUwNDM1XHUwNDQwXHUwNDMyXHUwNDM1XHUwNDQwXSJ9', '/path/cssold/', '/servers/cssold/', '/path/update/cssold/', '500:1000', '66:100', '512', 0, '15:30:90:180', '7:30:90:180', 0, 0, 1, 'de_dust2', 1, 0, 1, 0, 1, 0, '', 15000, 0, 'eyI2Nl81MDAiOiIyNyIsIjEwMF81MDAiOiIzNSIsIjY2XzEwMDAiOiI0NSIsIjEwMF8xMDAwIjoiNTUifQ==', '', '', 1, 1),
(3, 1, 'css', 'Обычный', 10, 64, 27218, 27318, 'Новый игровой сервер', 'eyJzdGVhbSI6IlN0ZWFtIFtcdTA0MjdcdTA0MzhcdTA0NDFcdTA0NDJcdTA0NGJcdTA0MzkgXHUwNDQxXHUwNDM1XHUwNDQwXHUwNDMyXHUwNDM1XHUwNDQwXSJ9', '/path/css/', '/servers/css/', '/path/update/css/', '500:1000', '66:100', '512', 0, '15:30:90:180', '7:30:90:180', 0, 0, 1, 'de_dust2', 1, 0, 1, 0, 1, 0, '', 15000, 0, '27:35', '', '', 1, 1),
(4, 1, 'csgo', 'Обычный', 10, 64, 27319, 27419, 'Новый игровой сервер', 'eyJzdGVhbSI6IlN0ZWFtIFtcdTA0MjdcdTA0MzhcdTA0NDFcdTA0NDJcdTA0NGJcdTA0MzkgXHUwNDQxXHUwNDM1XHUwNDQwXHUwNDMyXHUwNDM1XHUwNDQwXSJ9', '/path/csgo/', '/servers/csgo/', '/path/update/csgo/', '500:1000', '64:128', '1024', 0, '15:30:90:180', '7:30:90:180', 0, 0, 1, 'de_dust2', 1, 0, 1, 0, 1, 0, '', 30000, 0, '40:55', '', '', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `cs` tinyint(1) NOT NULL DEFAULT '0',
  `cssold` tinyint(1) NOT NULL DEFAULT '0',
  `css` tinyint(1) NOT NULL DEFAULT '0',
  `csgo` tinyint(1) NOT NULL DEFAULT '0',
  `samp` tinyint(1) NOT NULL DEFAULT '0',
  `crmp` tinyint(1) NOT NULL DEFAULT '0',
  `mta` tinyint(1) NOT NULL DEFAULT '0',
  `mc` tinyint(1) NOT NULL DEFAULT '0',
  `ram` int(11) NOT NULL,
  `test` int(11) NOT NULL,
  `show` tinyint(1) NOT NULL,
  `sort` int(11) NOT NULL,
  `domain` varchar(40) NOT NULL DEFAULT '',
  `ddos` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` char(15) NOT NULL,
  `passwd` char(32) NOT NULL,
  `name` char(32) NOT NULL,
  `lastname` char(32) NOT NULL,
  `patronymic` char(32) NOT NULL,
  `mail` char(50) NOT NULL,
  `new_mail` char(50) NOT NULL DEFAULT '',
  `confirm_mail` char(32) NOT NULL DEFAULT '',
  `phone` char(12) NOT NULL,
  `confirm_phone` int(6) NOT NULL DEFAULT '0',
  `contacts` varchar(100) NOT NULL,
  `balance` float NOT NULL,
  `wmr` char(13) NOT NULL DEFAULT '',
  `group` char(7) NOT NULL,
  `support_info` varchar(50) NOT NULL DEFAULT '',
  `level` int(11) NOT NULL DEFAULT '0',
  `ip` char(16) NOT NULL DEFAULT '',
  `browser` char(20) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL,
  `part` int(11) NOT NULL DEFAULT '0',
  `part_money` float NOT NULL DEFAULT '0',
  `security_ip` tinyint(1) NOT NULL DEFAULT '0',
  `security_code` tinyint(1) NOT NULL DEFAULT '0',
  `notice_help` tinyint(1) NOT NULL DEFAULT '0',
  `notice_news` tinyint(1) NOT NULL DEFAULT '1',
  `help` tinyint(1) NOT NULL DEFAULT '0',
  `rental` varchar(4) NOT NULL DEFAULT '0',
  `extend` varchar(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `passwd`, `name`, `lastname`, `patronymic`, `mail`, `new_mail`, `confirm_mail`, `phone`, `confirm_phone`, `contacts`, `balance`, `wmr`, `group`, `support_info`, `level`, `ip`, `browser`, `time`, `date`, `part`, `part_money`, `security_ip`, `security_code`, `notice_help`, `notice_news`, `help`, `rental`, `extend`) VALUES
(1, 'root', 'ENGINEGPHASH', 'Имя', 'Фамилия', 'Отчество', 'admin@enginegp.ru', '', '', '', 0, '', 10000, '', 'admin', '', 0, '127.0.0.1', 'Google Chrome', 1518459967, 1517667554, 0, 0, 0, 0, 0, 1, 0, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `web`
--

CREATE TABLE `web` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL,
  `desing` varchar(32) NOT NULL DEFAULT '',
  `server` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `domain` varchar(40) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `config` text NOT NULL,
  `login` varchar(32) NOT NULL DEFAULT '',
  `update` int(11) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `wiki_answer`
--

CREATE TABLE `wiki_answer` (
  `wiki` int(11) NOT NULL,
  `cat` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `wiki_category`
--

CREATE TABLE `wiki_category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
-- Индексы таблицы `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control`
--
ALTER TABLE `control`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_admins_cs`
--
ALTER TABLE `control_admins_cs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_admins_csgo`
--
ALTER TABLE `control_admins_csgo`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_admins_css`
--
ALTER TABLE `control_admins_css`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_admins_cssold`
--
ALTER TABLE `control_admins_cssold`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_boost`
--
ALTER TABLE `control_boost`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_copy`
--
ALTER TABLE `control_copy`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_crontab`
--
ALTER TABLE `control_crontab`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_firewall`
--
ALTER TABLE `control_firewall`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_plugins_buy`
--
ALTER TABLE `control_plugins_buy`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_plugins_install`
--
ALTER TABLE `control_plugins_install`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `control_servers`
--
ALTER TABLE `control_servers`
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
-- Индексы таблицы `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `jobs_app`
--
ALTER TABLE `jobs_app`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
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
-- AUTO_INCREMENT для таблицы `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `control`
--
ALTER TABLE `control`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_admins_cs`
--
ALTER TABLE `control_admins_cs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_boost`
--
ALTER TABLE `control_boost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_copy`
--
ALTER TABLE `control_copy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_crontab`
--
ALTER TABLE `control_crontab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_firewall`
--
ALTER TABLE `control_firewall`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_plugins_buy`
--
ALTER TABLE `control_plugins_buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_plugins_install`
--
ALTER TABLE `control_plugins_install`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `control_servers`
--
ALTER TABLE `control_servers`
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
-- AUTO_INCREMENT для таблицы `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `jobs_app`
--
ALTER TABLE `jobs_app`
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
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
