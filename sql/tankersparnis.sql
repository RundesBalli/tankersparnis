-- Adminer 4.7.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `tankersparnis`;
CREATE DATABASE `tankersparnis` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `tankersparnis`;

DELIMITER ;;

CREATE EVENT `Nicht aktivierte Accounts löschen` ON SCHEDULE EVERY 1 HOUR STARTS '2021-06-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht 1x stündlich alle nicht aktivierten Accounts, 24 Stunden' DO DELETE FROM `users` WHERE `active`=0 AND `registered` < DATE_SUB(NOW(), INTERVAL 1 DAY);;

CREATE EVENT `Sitzungsbereinigung` ON SCHEDULE EVERY 6 HOUR STARTS '2021-06-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht 4x täglich abgelaufene Sitzungen nach sechs Wochen' DO DELETE FROM `sessions` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 WEEK);;

DELIMITER ;

DROP TABLE IF EXISTS `failedEmails`;
CREATE TABLE `failedEmails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `to` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'E-Mail Adresse',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Betreff',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nachrichteninhalt',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Zustellversuchs',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `timestamp` (`timestamp`),
  CONSTRAINT `failedEmails_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fehlgeschlage E-Mail Sendungen';


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis - users.id',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrags',
  `loglevel` int(10) unsigned NOT NULL COMMENT 'Querverweis - loglevel.id',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Logtext (optional)',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `loglevel` (`loglevel`),
  CONSTRAINT `log_ibfk_2` FOREIGN KEY (`loglevel`) REFERENCES `logLevel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_3` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `logLevel`;
CREATE TABLE `logLevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Meldungsart',
  `cssClass` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HexCode der Meldungsfarbe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle - Loglevel Farben';

TRUNCATE `logLevel`;
INSERT INTO `logLevel` (`id`, `title`, `cssClass`) VALUES
(1,	'User-/Systemaktion',	'log-user'),
(2,	'Eintrag hinzugefügt',	'log-addEntry'),
(3,	'Eintrag bearbeitet',	'log-editEntry'),
(4,	'Eintrag gelöscht',	'log-delEntry'),
(5,	'Einstellungen geändert',	'log-settings');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sitzungshash',
  `lastActivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `hash` (`hash`),
  KEY `lastActivity` (`lastActivity`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passworthash',
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passwortsalt',
  `registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Registrierungszeitpunkt',
  `registerHash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash zum Aktivieren des Accounts',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Account aktiviert',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usertabelle';


-- 2021-06-06 21:11:24
