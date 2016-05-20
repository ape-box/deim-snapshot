-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generato il: Feb 14, 2012 alle 21:54
-- Versione del server: 5.5.20
-- Versione PHP: 5.3.9

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dbname`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `t_estate`
--

CREATE TABLE IF NOT EXISTS `t_estate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pub` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `ad_code` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_estate_spec`
--

CREATE TABLE IF NOT EXISTS `t_estate_spec` (
  `id` int(11) NOT NULL,
  `type` varchar(256) DEFAULT NULL,
  `province` varchar(6) DEFAULT NULL,
  `district` varchar(256) DEFAULT NULL,
  `address` varchar(256) DEFAULT NULL,
  `category` varchar(256) DEFAULT NULL,
  `kind` varchar(256) DEFAULT NULL,
  `metrics` varchar(256) DEFAULT NULL,
  `rooms` varchar(256) DEFAULT NULL,
  `bathrooms` varchar(256) DEFAULT NULL,
  `balcony` varchar(3) DEFAULT NULL,
  `heating` varchar(256) DEFAULT NULL,
  `elevator` varchar(3) DEFAULT NULL,
  `parking` varchar(3) DEFAULT NULL,
  `garage` varchar(3) DEFAULT NULL,
  `floor` varchar(256) DEFAULT NULL,
  `floors` varchar(256) DEFAULT NULL,
  `state` varchar(256) DEFAULT NULL,
  `build_year` varchar(256) DEFAULT NULL,
  `deed` varchar(256) DEFAULT NULL,
  `monthly_charges` varchar(256) DEFAULT NULL,
  `price` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_estate_text`
--

CREATE TABLE IF NOT EXISTS `t_estate_text` (
  `id` int(11) NOT NULL,
  `locale` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'it_IT',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `text` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_media_files`
--

CREATE TABLE IF NOT EXISTS `t_media_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `parent_table` varchar(256) DEFAULT NULL,
  `relation` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `mime` varchar(256) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `data` mediumblob NOT NULL,
  `checksum` varchar(32) NOT NULL,
  `serialized_params` text,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`,`parent_table`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_menu`
--

CREATE TABLE IF NOT EXISTS `t_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_menu_text`
--

CREATE TABLE IF NOT EXISTS `t_menu_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `locale` varchar(6) NOT NULL,
  `label` varchar(256) NOT NULL,
  `link` varchar(256) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`,`locale`),
  KEY `page_id` (`page_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_page`
--

CREATE TABLE IF NOT EXISTS `t_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_page_text`
--

CREATE TABLE IF NOT EXISTS `t_page_text` (
  `id` int(11) NOT NULL,
  `locale` varchar(6) NOT NULL,
  `title` varchar(256) NOT NULL,
  `text` text NOT NULL,
  `keywords` varchar(256) NOT NULL,
  `description` varchar(512) NOT NULL,
  PRIMARY KEY (`id`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `t_user`
--

CREATE TABLE IF NOT EXISTS `t_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `t_estate_spec`
--
ALTER TABLE `t_estate_spec`
  ADD CONSTRAINT `t_estate_spec_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_estate` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `t_estate_text`
--
ALTER TABLE `t_estate_text`
  ADD CONSTRAINT `fk_t_estate_id` FOREIGN KEY (`id`) REFERENCES `t_estate` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `t_menu_text`
--
ALTER TABLE `t_menu_text`
  ADD CONSTRAINT `t_menu_text_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `t_menu_text_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `t_menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `t_page_text`
--
ALTER TABLE `t_page_text`
  ADD CONSTRAINT `t_page_text_ibfk_1` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_2` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_3` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_4` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_5` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_6` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_7` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_page_text_ibfk_8` FOREIGN KEY (`id`) REFERENCES `t_page` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
