-- Adminer 4.8.1 MySQL 5.5.5-10.1.48-MariaDB-0+deb9u2 dump

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

CREATE EVENT `Rohdaten entfernen` ON SCHEDULE EVERY 1 HOUR STARTS '2022-02-03 22:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Rohdatenbereinigung. Erhalten des Preises' DO UPDATE `entries` SET `raw`=REGEXP_REPLACE(REGEXP_REPLACE(REGEXP_REPLACE(`raw`, '.+dist.+?,', ''), ',.+', ''), '\"', '') WHERE `raw` LIKE '{\"id\":%' AND `timestamp` < DATE_SUB(NOW(), INTERVAL 1 MONTH);;

CREATE EVENT `Sitzungsbereinigung` ON SCHEDULE EVERY 6 HOUR STARTS '2021-06-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht 4x täglich abgelaufene Sitzungen nach sechs Wochen' DO DELETE FROM `sessions` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 WEEK);;

DELIMITER ;

DROP TABLE IF EXISTS `cars`;
CREATE TABLE `cars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung des Fahrzeugs',
  `fuel` int(10) unsigned NOT NULL COMMENT 'Querverweis fuels.id',
  `fuelCompare` int(10) unsigned NOT NULL COMMENT 'Querverweis fuels.id',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `fuel` (`fuel`),
  KEY `fuelCompare` (`fuelCompare`),
  CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cars_ibfk_2` FOREIGN KEY (`fuel`) REFERENCES `fuels` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `cars_ibfk_3` FOREIGN KEY (`fuelCompare`) REFERENCES `fuelsCompare` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Autotabelle';


DROP TABLE IF EXISTS `entries`;
CREATE TABLE `entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `carId` int(10) unsigned NOT NULL COMMENT 'Querverweis cars.id',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrages',
  `fuelQuantity` double(7,2) unsigned NOT NULL COMMENT 'Getankte Menge Kraftstoff',
  `range` double(7,1) unsigned NOT NULL COMMENT 'Gefahrene Kilometer',
  `cost` double(7,2) unsigned NOT NULL COMMENT 'Kosten für Tankvorgang',
  `moneySaved` double(7,2) unsigned NOT NULL COMMENT 'Gesparter Betrag gegenüber dem Vergleichskraftstoff',
  `raw` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Rohergebnis der API Abfrage',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `carId` (`carId`),
  KEY `timestamp` (`timestamp`),
  CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`carId`) REFERENCES `cars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `entries_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ersparnistabelle';


DROP TABLE IF EXISTS `failedEmails`;
CREATE TABLE `failedEmails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `to` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'E-Mail Adresse',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Betreff',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nachrichteninhalt',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Zustellversuchs',
  `retryAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des frühesten nächsten Zustellversuchs',
  `retryCounter` int(2) unsigned NOT NULL DEFAULT '0' COMMENT 'Anzahl der Zustellversuche',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `timestamp` (`timestamp`),
  CONSTRAINT `failedEmails_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fehlgeschlage E-Mail Sendungen';


DROP TABLE IF EXISTS `fuels`;
CREATE TABLE `fuels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung des Kraftstoffs',
  `energy` double(7,2) unsigned NOT NULL COMMENT 'Energie des Kraftstoffs in kWh/l bzw. kWh/kg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kraftstofftabelle';

TRUNCATE `fuels`;
INSERT INTO `fuels` (`id`, `name`, `energy`) VALUES
(1,	'Autogas (LPG)',	6.90),
(2,	'Flüssigerdgas (LNG)',	13.80),
(3,	'Erdgas (CNG)',	13.30);

DROP TABLE IF EXISTS `fuelsCompare`;
CREATE TABLE `fuelsCompare` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung des Kraftstoffs',
  `energy` double(7,2) unsigned NOT NULL COMMENT 'Energie des Kraftstoffs in kWh/l',
  `symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kraftstoffbezeichnung für die API',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kraftstofftabelle zum Vergleich';

TRUNCATE `fuelsCompare`;
INSERT INTO `fuelsCompare` (`id`, `name`, `energy`, `symbol`) VALUES
(1,	'Benzin/Super, e5',	8.60,	'e5'),
(2,	'Diesel',	9.70,	'diesel');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log Tabelle';


DROP TABLE IF EXISTS `logLevel`;
CREATE TABLE `logLevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Meldungsart',
  `color` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HexCode der Meldungsfarbe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle - Loglevel Farben';

TRUNCATE `logLevel`;
INSERT INTO `logLevel` (`id`, `title`, `color`) VALUES
(1,	'User-/Systemaktion',	'808080'),
(2,	'Eintrag hinzugefügt',	'7BFF00'),
(4,	'Eintrag gelöscht',	'E80000'),
(5,	'Einstellungen geändert',	'0088FF'),
(6,	'KFZ hinzugefügt',	'7BFF00'),
(7,	'KFZ bearbeitet',	'FFAA00'),
(8,	'KFZ gelöscht',	'E80000');

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name der Berechtigung',
  `description` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Beschreibung der Berechtigung',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Berechtigungen';


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sitzungstabelle';


DROP TABLE IF EXISTS `userPermissions`;
CREATE TABLE `userPermissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis users.id',
  `permissionId` int(10) unsigned NOT NULL COMMENT 'Querverweis permissions.id',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `permissionId` (`permissionId`),
  CONSTRAINT `userPermissions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userPermissions_ibfk_2` FOREIGN KEY (`permissionId`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Userberechtigungen';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passworthash',
  `salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passwortsalt',
  `registered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Registrierung',
  `registerHash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash zum Aktivieren des Accounts',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Account aktiviert',
  `lastActivity` datetime DEFAULT NULL COMMENT 'Zeitpunkt der letzten Aktivität',
  `reminderDate` datetime DEFAULT NULL COMMENT 'Zeitpunkt an dem die "6 Monate Inaktivität" Email verschickt wurde',
  `preventPasswordReset` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Passwort zurücksetzen verhindern',
  `lastPwReset` datetime DEFAULT NULL COMMENT 'Zeitpunkt des letzten Passwort Resets',
  `validEmail` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 = Unbestätigte Email',
  `emailHash` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hash zum Aktivieren der neuen E-Mail Adresse',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `active` (`active`),
  KEY `lastActivity` (`lastActivity`),
  KEY `reminderDate` (`reminderDate`),
  KEY `preventPasswordReset` (`preventPasswordReset`),
  KEY `lastPwReset` (`lastPwReset`),
  KEY `validEmail` (`validEmail`),
  KEY `emailHash` (`emailHash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usertabelle';


-- 2022-02-12 22:49:58
