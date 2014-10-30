CREATE DATABASE  IF NOT EXISTS `$DBNAME` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `$DBNAME`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: 127.0.0.1    Database: $DBNAME
-- ------------------------------------------------------
-- Server version	5.6.14

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `addays`
--

DROP TABLE IF EXISTS `addays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addays` (
  `AdIdRef` int(11) unsigned NOT NULL,
  `Day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `AdDayId` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`AdDayId`),
  KEY `REF` (`AdIdRef`),
  CONSTRAINT `REF` FOREIGN KEY (`AdIdRef`) REFERENCES `adrotation` (`RotationNum`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=308 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admininfo`
--

DROP TABLE IF EXISTS `admininfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admininfo` (
  `idAutoFlag` int(14) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `EventCode` int(14) unsigned NOT NULL,
  `Creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EpNum` int(13) unsigned zerofill NOT NULL,
  PRIMARY KEY (`idAutoFlag`),
  UNIQUE KEY `idAutoFlag_UNIQUE` (`idAutoFlag`),
  KEY `Episode` (`EpNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains audit information for programs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adrotation`
--

DROP TABLE IF EXISTS `adrotation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adrotation` (
  `RotationNum` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `startTime` time NOT NULL DEFAULT '00:00:00',
  `endTime` time NOT NULL DEFAULT '24:00:00',
  `BlockLimit` int(11) NOT NULL DEFAULT '1',
  `AdId` int(11) NOT NULL,
  `HourlyLimit` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`RotationNum`),
  UNIQUE KEY `RotationNum_UNIQUE` (`RotationNum`),
  KEY `AdRef` (`AdId`),
  CONSTRAINT `AdRef` FOREIGN KEY (`AdId`) REFERENCES `adverts` (`AdId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8 COMMENT='Contains information pertaining to Ads required to be played';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adverts`
--

DROP TABLE IF EXISTS `adverts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adverts` (
  `AdId` int(11) NOT NULL AUTO_INCREMENT,
  `Category` int(11) NOT NULL,
  `Length` int(11) DEFAULT '0',
  `EndDate` date DEFAULT '9999-12-31',
  `StartDate` date DEFAULT '0001-01-01',
  `Playcount` bigint(20) DEFAULT '0',
  `AdName` varchar(45) NOT NULL,
  `Rotation` int(10) unsigned DEFAULT NULL,
  `Active` int(1) unsigned NOT NULL DEFAULT '1',
  `Friend` int(1) NOT NULL DEFAULT '0',
  `Language` varchar(45) NOT NULL DEFAULT 'English',
  `XREF` int(11) DEFAULT NULL,
  `Limit` int(11) DEFAULT NULL,
  `file` text,
  `ClientID` bigint(20) unsigned DEFAULT NULL,
  `last_reset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AdId`,`Category`),
  KEY `Client_idx` (`ClientID`),
  CONSTRAINT `Client` FOREIGN KEY (`ClientID`) REFERENCES `clients` (`ClientNumber`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=182 DEFAULT CHARSET=utf8 COMMENT='contains information on available Ads and Friends';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `airs`
--

DROP TABLE IF EXISTS `airs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `airs` (
  `idairs` int(11) NOT NULL,
  `program` varchar(75) NOT NULL,
  `start_day` int(7) NOT NULL,
  `end_day` int(7) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`idairs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Air Times for programs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `ClientNumber` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(80) NOT NULL,
  `CreditLimit` double NOT NULL DEFAULT '5000',
  `PaymentTerms` int(10) NOT NULL DEFAULT '1',
  `Address` text,
  `ContactName` varchar(80) NOT NULL DEFAULT 'Manager / Owner',
  `PhoneNumber` varchar(45) DEFAULT NULL,
  `Status` enum('OVL','ACT','EXP','COL','INT','CLO','SUS') NOT NULL DEFAULT 'SUS',
  PRIMARY KEY (`ClientNumber`),
  UNIQUE KEY `ClientNumber_UNIQUE` (`ClientNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `device_codes`
--

DROP TABLE IF EXISTS `device_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device_codes` (
  `device_code` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Manufacturer` varchar(45) DEFAULT NULL,
  `Code_Description` varchar(45) DEFAULT NULL,
  `Device` varchar(45) NOT NULL,
  `Command` varchar(100) NOT NULL,
  `command_type` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`device_code`),
  UNIQUE KEY `device_UNIQUE` (`device_code`),
  UNIQUE KEY `Duplicate` (`Device`,`Command`,`command_type`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dj`
--

DROP TABLE IF EXISTS `dj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dj` (
  `Alias` varchar(50) NOT NULL,
  `djname` varchar(45) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `years` int(11) DEFAULT NULL,
  `email_block` int(2) DEFAULT NULL,
  `member_ref` int(11) DEFAULT NULL,
  PRIMARY KEY (`Alias`),
  KEY `Member_idx` (`member_ref`),
  CONSTRAINT `Member` FOREIGN KEY (`member_ref`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the DJ information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emergency_alerts`
--

DROP TABLE IF EXISTS `emergency_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergency_alerts` (
  `AlertId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `EpNum` int(13) unsigned NOT NULL,
  `Reference` varchar(45) DEFAULT NULL,
  `Source` varchar(45) DEFAULT NULL,
  `Acknowledged` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`AlertId`),
  UNIQUE KEY `AlertId_UNIQUE` (`AlertId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `episode`
--

DROP TABLE IF EXISTS `episode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `episode` (
  `callsign` varchar(4) NOT NULL,
  `programname` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time DEFAULT NULL,
  `prerecorddate` date DEFAULT NULL,
  `totalspokentime` double unsigned DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `Lock` int(3) unsigned NOT NULL DEFAULT '0' COMMENT 'LOCK - 1 = Finalized; 2 = Admin Lock; 3 = Audit Lock; 0 = No Lock',
  `Type` int(2) NOT NULL DEFAULT '0' COMMENT 'Determines the following: 0=Live, 1=PreRecord, 2=Timeless',
  `EpNum` int(13) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `Guests` varchar(45) DEFAULT NULL,
  `EndStamp` timestamp NULL DEFAULT NULL,
  `LastAccess` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`callsign`,`programname`,`date`,`starttime`),
  UNIQUE KEY `EpNum_UNIQUE` (`EpNum`),
  KEY `programref` (`callsign`,`programname`),
  KEY `Program` (`callsign`,`programname`),
  CONSTRAINT `Program` FOREIGN KEY (`callsign`, `programname`) REFERENCES `program` (`callsign`, `programname`)
) ENGINE=InnoDB AUTO_INCREMENT=4417 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventcode`
--

DROP TABLE IF EXISTS `eventcode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventcode` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idnew_table_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genre` (
  `genreid` varchar(20) NOT NULL,
  `cancon` int(11) unsigned NOT NULL,
  `playlist` int(11) unsigned NOT NULL,
  `canconperc` float unsigned NOT NULL DEFAULT '0.4',
  `playlistperc` float unsigned NOT NULL DEFAULT '0.35',
  `UID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PlType` int(2) unsigned NOT NULL DEFAULT '1',
  `CCType` int(2) unsigned NOT NULL DEFAULT '1',
  `Station` varchar(4) NOT NULL,
  PRIMARY KEY (`genreid`),
  UNIQUE KEY `UID_UNIQUE` (`UID`),
  KEY `STATION_idx` (`Station`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='Contains the Genre information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hardware`
--

DROP TABLE IF EXISTS `hardware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hardware` (
  `hardwareid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `barcode` varchar(45) DEFAULT NULL,
  `ipv4_address` varchar(16) NOT NULL,
  `friendly_name` varchar(45) DEFAULT NULL,
  `hardware_type` int(5) NOT NULL DEFAULT '0',
  `room` int(10) DEFAULT NULL,
  `station` varchar(4) DEFAULT NULL,
  `in_service` int(2) unsigned NOT NULL DEFAULT '1',
  `device_code` varchar(45) DEFAULT NULL,
  `port` int(8) unsigned DEFAULT NULL,
  PRIMARY KEY (`hardwareid`),
  UNIQUE KEY `hardwareid_UNIQUE` (`hardwareid`),
  KEY `Device_idx` (`device_code`),
  CONSTRAINT `Device` FOREIGN KEY (`device_code`) REFERENCES `device_codes` (`Device`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hardwareevents`
--

DROP TABLE IF EXISTS `hardwareevents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hardwareevents` (
  `idHardwareEvents` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Event` varchar(45) DEFAULT NULL,
  `Port` varchar(45) DEFAULT NULL,
  `SystemID` varchar(45) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHardwareEvents`),
  UNIQUE KEY `idHardwareEvents_UNIQUE` (`idHardwareEvents`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `callsign` varchar(4) NOT NULL,
  `programname` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `songid` int(50) NOT NULL,
  `languageid` varchar(45) NOT NULL DEFAULT 'english',
  PRIMARY KEY (`callsign`,`programname`,`date`,`starttime`,`songid`,`languageid`),
  KEY `song` (`callsign`,`programname`,`date`,`starttime`,`songid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `library`
--

DROP TABLE IF EXISTS `library`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `library` (
  `Barcode` varchar(45) DEFAULT NULL,
  `datein` date DEFAULT NULL,
  `dateout` date DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `artist` varchar(100) NOT NULL,
  `album` varchar(100) NOT NULL,
  `variousartists` int(10) unsigned DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `condition` varchar(45) DEFAULT NULL,
  `genre` varchar(45) DEFAULT NULL,
  `status` int(3) DEFAULT NULL,
  `labelid` bigint(20) unsigned NOT NULL,
  `playlistid` bigint(20) unsigned DEFAULT NULL,
  `Locale` enum('Local','County','State','Province','Country','International') NOT NULL DEFAULT 'International',
  `CanCon` int(1) unsigned NOT NULL DEFAULT '0',
  `RefCode` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `HasPL` enum('TRUE','FALSE','NA','PRE') NOT NULL DEFAULT 'NA',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RefCode`),
  UNIQUE KEY `RefCode_UNIQUE` (`RefCode`),
  UNIQUE KEY `library_id` (`album`,`artist`,`datein`),
  KEY `Playlist_idx` (`playlistid`),
  KEY `Label_idx` (`labelid`),
  CONSTRAINT `Playlist` FOREIGN KEY (`playlistid`) REFERENCES `playlist` (`PlaylistId`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `Record` FOREIGN KEY (`labelid`) REFERENCES `recordlabel` (`LabelNumber`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=442536 DEFAULT CHARSET=utf8 COMMENT='Contains Information regarding Archived / Stored information';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL,
  `access` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `on_air`
--

DROP TABLE IF EXISTS `on_air`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `on_air` (
  `instance` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SourceAir` int(4) unsigned NOT NULL,
  `SourceRecord` int(4) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`instance`),
  UNIQUE KEY `instance_UNIQUE` (`instance`)
) ENGINE=InnoDB AUTO_INCREMENT=27918 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `performs`
--

DROP TABLE IF EXISTS `performs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `performs` (
  `callsign` varchar(4) NOT NULL,
  `programname` varchar(75) NOT NULL,
  `Alias` varchar(50) NOT NULL,
  `STdate` datetime DEFAULT '0001-01-01 00:00:00',
  `ENdate` datetime DEFAULT '9000-01-01 00:00:00',
  PRIMARY KEY (`callsign`,`programname`,`Alias`),
  KEY `progref` (`callsign`,`programname`),
  KEY `djref` (`Alias`),
  CONSTRAINT `djref` FOREIGN KEY (`Alias`) REFERENCES `dj` (`Alias`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `progref` FOREIGN KEY (`callsign`, `programname`) REFERENCES `program` (`callsign`, `programname`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `playlist`
--

DROP TABLE IF EXISTS `playlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playlist` (
  `ZoneCode` varchar(10) DEFAULT NULL COMMENT 'Second Generation Playlist Numbering (ie. D0043 is valid as well as DPL_SERV002)',
  `ZoneNumber` int(10) DEFAULT NULL,
  `SmallCode` int(10) unsigned NOT NULL COMMENT 'IE Version 1 (numerical Only)',
  `Activate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Expire` timestamp NULL DEFAULT NULL,
  `PlaylistId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `RefCode` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`PlaylistId`),
  UNIQUE KEY `playlistid_UNIQUE` (`PlaylistId`),
  KEY `Library_idx` (`RefCode`),
  CONSTRAINT `Lib_Ref` FOREIGN KEY (`RefCode`) REFERENCES `library` (`RefCode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `program`
--

DROP TABLE IF EXISTS `program`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program` (
  `callsign` varchar(4) NOT NULL,
  `programname` varchar(75) NOT NULL,
  `length` int(11) DEFAULT NULL,
  `syndicatesource` varchar(45) DEFAULT NULL,
  `genre` varchar(20) DEFAULT 'Eclectic',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `Airtime` time DEFAULT NULL,
  `CCX` int(11) NOT NULL DEFAULT '-1',
  `PLX` int(11) NOT NULL DEFAULT '-1',
  `HitLimit` int(11) NOT NULL DEFAULT '0',
  `SponsId` int(11) DEFAULT NULL,
  `displayorder` varchar(20) NOT NULL DEFAULT 'desc',
  `Theme` int(11) unsigned NOT NULL DEFAULT '8',
  `ProgramID` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `Display_Order` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`programname`,`callsign`),
  UNIQUE KEY `programname_UNIQUE` (`programname`),
  UNIQUE KEY `ProgramID_UNIQUE` (`ProgramID`),
  KEY `callsign` (`callsign`),
  KEY `genre` (`genre`),
  KEY `Sponsor` (`SponsId`),
  CONSTRAINT `callsign` FOREIGN KEY (`callsign`) REFERENCES `station` (`callsign`),
  CONSTRAINT `Sponsor` FOREIGN KEY (`SponsId`) REFERENCES `adverts` (`AdId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8 COMMENT='Programs are the actual programs that the Djs perform but no';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `promptlog`
--

DROP TABLE IF EXISTS `promptlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promptlog` (
  `idPromptLog` int(13) unsigned NOT NULL AUTO_INCREMENT,
  `EpNum` int(13) unsigned zerofill DEFAULT NULL,
  `AdNum` int(11) DEFAULT NULL,
  `PromptTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `PlayTime` datetime DEFAULT NULL,
  `SongNum` int(50) DEFAULT NULL,
  PRIMARY KEY (`idPromptLog`),
  UNIQUE KEY `idPromptLog_UNIQUE` (`idPromptLog`),
  KEY `Episode` (`EpNum`),
  KEY `AdRef` (`AdNum`),
  KEY `SongRef` (`SongNum`),
  KEY `Episode Link` (`EpNum`),
  KEY `Song Link` (`SongNum`),
  KEY `Ad Link` (`AdNum`),
  KEY `Song` (`SongNum`),
  CONSTRAINT `Song` FOREIGN KEY (`SongNum`) REFERENCES `song` (`songid`)
) ENGINE=InnoDB AUTO_INCREMENT=17006 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rds`
--

DROP TABLE IF EXISTS `rds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rds` (
  `rds_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rds_status` varchar(10) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(100) DEFAULT NULL,
  `type` float DEFAULT NULL,
  PRIMARY KEY (`rds_id`),
  UNIQUE KEY `RDS_id_UNIQUE` (`rds_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81846 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recordlabel`
--

DROP TABLE IF EXISTS `recordlabel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recordlabel` (
  `LabelNumber` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL DEFAULT 'DefaultLabel',
  `Location` int(20) DEFAULT NULL,
  `Size` int(4) NOT NULL DEFAULT '1',
  `name_alias_duplicate` bigint(20) unsigned DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `verified` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`LabelNumber`,`Name`),
  UNIQUE KEY `idRecordLabel_UNIQUE` (`LabelNumber`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=152520 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `replicates`
--

DROP TABLE IF EXISTS `replicates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replicates` (
  `idReplicates` int(10) unsigned NOT NULL,
  `ReferenceEpisode` int(13) unsigned zerofill NOT NULL,
  `OverrideTimestamp` datetime NOT NULL,
  PRIMARY KEY (`idReplicates`),
  KEY `Episode_idx` (`ReferenceEpisode`),
  CONSTRAINT `episodenum` FOREIGN KEY (`ReferenceEpisode`) REFERENCES `episode` (`EpNum`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `socan`
--

DROP TABLE IF EXISTS `socan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `socan` (
  `AuditId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Enabled` tinyint(4) NOT NULL DEFAULT '0',
  `RQArtist` tinyint(4) NOT NULL DEFAULT '1',
  `RQComposer` tinyint(4) NOT NULL DEFAULT '1',
  `RQAlbum` tinyint(4) NOT NULL DEFAULT '1',
  `start` date NOT NULL DEFAULT '0001-01-01',
  `end` date NOT NULL DEFAULT '9999-12-30',
  `RQAfterHr` tinyint(4) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ShowPrompt` tinyint(4) NOT NULL DEFAULT '1',
  `StationID` varchar(4) NOT NULL,
  `Description` text,
  PRIMARY KEY (`AuditId`),
  UNIQUE KEY `AuditId_UNIQUE` (`AuditId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Setings of a Socan or Resound Audit';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `song`
--

DROP TABLE IF EXISTS `song`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `song` (
  `songid` int(50) NOT NULL AUTO_INCREMENT,
  `callsign` varchar(4) NOT NULL,
  `programname` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `instrumental` int(10) unsigned NOT NULL DEFAULT '0',
  `time` time DEFAULT NULL,
  `album` varchar(100) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `artist` varchar(100) DEFAULT NULL,
  `cancon` int(10) unsigned NOT NULL DEFAULT '0',
  `playlistnumber` int(10) unsigned DEFAULT NULL,
  `category` int(10) unsigned NOT NULL,
  `hit` int(10) unsigned NOT NULL DEFAULT '0',
  `Spoken` decimal(4,2) DEFAULT NULL,
  `composer` varchar(100) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `AdViolationFlag` int(3) DEFAULT NULL,
  `barcode` varchar(45) DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Resords the last access time for the record',
  `RefCode` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`songid`,`callsign`,`programname`,`date`,`starttime`),
  KEY `episode` (`callsign`,`programname`,`date`,`starttime`),
  KEY `library_idx` (`RefCode`),
  CONSTRAINT `episode` FOREIGN KEY (`callsign`, `programname`, `date`, `starttime`) REFERENCES `episode` (`callsign`, `programname`, `date`, `starttime`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `library` FOREIGN KEY (`RefCode`) REFERENCES `library` (`RefCode`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=96498 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `station`
--

DROP TABLE IF EXISTS `station`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `station` (
  `callsign` varchar(4) NOT NULL,
  `stationname` varchar(45) NOT NULL,
  `Designation` varchar(45) DEFAULT NULL,
  `frequency` varchar(10) DEFAULT NULL,
  `website` varchar(45) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `boothphone` varchar(45) DEFAULT NULL,
  `directorphone` varchar(45) DEFAULT NULL,
  `ST_DefaultSort` varchar(45) NOT NULL DEFAULT 'ASC',
  `ST_PLLG` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Playlist Group Live Setting',
  `ST_ForceComposer` int(1) unsigned NOT NULL DEFAULT '0',
  `ST_ForceArtist` int(1) unsigned NOT NULL DEFAULT '0',
  `ST_ForceAlbum` int(1) unsigned NOT NULL DEFAULT '0',
  `ST_ColorFail` varchar(45) NOT NULL DEFAULT '#FFFF00',
  `ST_ColorPass` varchar(45) NOT NULL DEFAULT '#90EE90',
  `ST_PLRG` int(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Playlist Live Grouping for Reports',
  `ST_DispCount` int(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Display Counters on Screen',
  `ST_ColorNote` varchar(45) NOT NULL DEFAULT '#ADD8E6',
  `managerphone` varchar(45) DEFAULT NULL,
  `ST_ADSH` int(10) unsigned NOT NULL DEFAULT '1',
  `ST_PSAH` int(10) unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`callsign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains information about stations';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `switchstatus`
--

DROP TABLE IF EXISTS `switchstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `switchstatus` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Bank1` varchar(45) NOT NULL DEFAULT '2',
  `Bank2` varchar(45) NOT NULL DEFAULT '2',
  `SS` varchar(8) NOT NULL DEFAULT 'S0S,2,2',
  `UID` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `idSwitchStatus_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=493354 DEFAULT CHARSET=utf8 COMMENT='Contains records of switch activity';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `switchstatus_BINS` BEFORE INSERT ON `switchstatus` FOR EACH ROW
-- Edit trigger body code below this line. Do not edit lines above this one
insert into `on_air` (`SourceAir`,`SourceRecord`) values ('1','1') */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER switch_trigger
AFTER INSERT ON switchstatus
FOR EACH ROW    
BEGIN    
  -- DECLARE result INT;    
  -- SET result = sys_exec('C:\php\php-cgi.exe C:\inetpub\Drupal\TPS\metaupdate\RDS.php');     
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `trafficaudit`
--

DROP TABLE IF EXISTS `trafficaudit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trafficaudit` (
  `idTrafficAudit` int(11) NOT NULL,
  `songid` int(50) NOT NULL,
  `advertid` int(11) NOT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `complete` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`idTrafficAudit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UserId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(45) NOT NULL,
  `Active` int(1) NOT NULL DEFAULT '0',
  `AccessLevel` varchar(1) DEFAULT NULL,
  `PWDMD5` varchar(100) NOT NULL,
  `DjAlias` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`UserId`,`Username`),
  UNIQUE KEY `UserId_UNIQUE` (`UserId`),
  UNIQUE KEY `Username_UNIQUE` (`Username`),
  KEY `Alias` (`DjAlias`),
  CONSTRAINT `Alias` FOREIGN KEY (`DjAlias`) REFERENCES `dj` (`Alias`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This contains the information that pertains to login of DJ''s';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database '%DBNAME'
--

--
-- Dumping routines for database '%DBNAME'
--
/*!50003 DROP FUNCTION IF EXISTS `levenshtein` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `levenshtein`( s1 VARCHAR(255), s2 VARCHAR(255) ) RETURNS int(11)
    DETERMINISTIC
BEGIN 
    DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT; 
    DECLARE s1_char CHAR; 
    -- max strlen=255 
    DECLARE cv0, cv1 VARBINARY(256); 
    SET s1_len = CHAR_LENGTH(s1), s2_len = CHAR_LENGTH(s2), cv1 = 0x00, j = 1, i = 1, c = 0; 
    IF s1 = s2 THEN 
      RETURN 0; 
    ELSEIF s1_len = 0 THEN 
      RETURN s2_len; 
    ELSEIF s2_len = 0 THEN 
      RETURN s1_len; 
    ELSE 
      WHILE j <= s2_len DO 
        SET cv1 = CONCAT(cv1, UNHEX(HEX(j))), j = j + 1; 
      END WHILE; 
      WHILE i <= s1_len DO 
        SET s1_char = SUBSTRING(s1, i, 1), c = i, cv0 = UNHEX(HEX(i)), j = 1; 
        WHILE j <= s2_len DO 
          SET c = c + 1; 
          IF s1_char = SUBSTRING(s2, j, 1) THEN  
            SET cost = 0; ELSE SET cost = 1; 
          END IF; 
          SET c_temp = CONV(HEX(SUBSTRING(cv1, j, 1)), 16, 10) + cost; 
          IF c > c_temp THEN SET c = c_temp; END IF; 
            SET c_temp = CONV(HEX(SUBSTRING(cv1, j+1, 1)), 16, 10) + 1; 
            IF c > c_temp THEN  
              SET c = c_temp;  
            END IF; 
            SET cv0 = CONCAT(cv0, UNHEX(HEX(c))), j = j + 1; 
        END WHILE; 
        SET cv1 = cv0, i = i + 1; 
      END WHILE; 
    END IF; 
    RETURN c; 
  END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `levenshtein_ratio` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `levenshtein_ratio`( s1 VARCHAR(255), s2 VARCHAR(255) ) RETURNS int(11)
    DETERMINISTIC
BEGIN 
    DECLARE s1_len, s2_len, max_len INT; 
    SET s1_len = LENGTH(s1), s2_len = LENGTH(s2); 
    IF s1_len > s2_len THEN  
      SET max_len = s1_len;  
    ELSE  
      SET max_len = s2_len;  
    END IF; 
    RETURN ROUND((1 - LEVENSHTEIN(s1, s2) / max_len) * 100); 
  END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-05 18:08:37
