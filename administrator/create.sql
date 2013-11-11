CREATE DATABASE  IF NOT EXISTS `ckxu` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ckxu`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: localhost    Database: ckxu
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
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='Contains information pertaining to Ads required to be played';
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
  `Playcount` bigint(20) unsigned DEFAULT '0',
  `AdName` varchar(45) NOT NULL,
  `Rotation` int(10) unsigned DEFAULT NULL,
  `Active` int(1) unsigned NOT NULL DEFAULT '1',
  `Friend` int(1) NOT NULL DEFAULT '0',
  `Language` varchar(45) NOT NULL DEFAULT 'English',
  `XREF` int(11) DEFAULT NULL,
  `Limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`AdId`,`Category`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COMMENT='contains information on available Ads and Friends';
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
  `Username` varchar(10) DEFAULT NULL,
  `password` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`Alias`),
  UNIQUE KEY `Username_UNIQUE` (`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the DJ information';
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
) ENGINE=InnoDB AUTO_INCREMENT=3206 DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`genreid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the Genre information';
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
  `Refcode` varchar(45) NOT NULL,
  `Barcode` varchar(45) DEFAULT NULL,
  `datein` date DEFAULT NULL,
  `dateout` date DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `artist` varchar(100) DEFAULT NULL,
  `album` varchar(100) DEFAULT NULL,
  `variousartists` int(10) unsigned DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `condition` varchar(45) DEFAULT NULL,
  `label` varchar(45) DEFAULT NULL,
  `genre` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Refcode`),
  UNIQUE KEY `Refcode_UNIQUE` (`Refcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains Information regarding Archived / Stored information';
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
  `number` int(10) unsigned NOT NULL,
  `Album` varchar(45) DEFAULT NULL,
  `Artist` varchar(45) NOT NULL,
  `Genre` varchar(25) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cancon` enum('LC','AC','CC','NC') NOT NULL DEFAULT 'NC' COMMENT 'LC = LocalContent, CC = Canadian Content, NULL = Not CC/LC',
  `label` enum('IL','SL','ML','LL') NOT NULL DEFAULT 'LL',
  `barcode` varchar(45) DEFAULT NULL,
  `refcode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`number`),
  UNIQUE KEY `number_UNIQUE` (`number`),
  KEY `library` (`refcode`),
  KEY `libRef` (`refcode`),
  CONSTRAINT `libRef` FOREIGN KEY (`refcode`) REFERENCES `library` (`Refcode`) ON DELETE NO ACTION ON UPDATE NO ACTION
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
  PRIMARY KEY (`programname`,`callsign`),
  UNIQUE KEY `programname_UNIQUE` (`programname`),
  UNIQUE KEY `ProgramID_UNIQUE` (`ProgramID`),
  KEY `callsign` (`callsign`),
  KEY `genre` (`genre`),
  KEY `Sponsor` (`SponsId`),
  CONSTRAINT `callsign` FOREIGN KEY (`callsign`) REFERENCES `station` (`callsign`),
  CONSTRAINT `Sponsor` FOREIGN KEY (`SponsId`) REFERENCES `adverts` (`AdId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COMMENT='Programs are the actual programs that the Djs perform but no';
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
) ENGINE=InnoDB AUTO_INCREMENT=9298 DEFAULT CHARSET=utf8;
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
  PRIMARY KEY (`AuditId`),
  UNIQUE KEY `AuditId_UNIQUE` (`AuditId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Setings of a Socan or Resound Audit';
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
  `refcode` varchar(45) DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Resords the last access time for the record',
  PRIMARY KEY (`songid`,`callsign`,`programname`,`date`,`starttime`),
  KEY `episode` (`callsign`,`programname`,`date`,`starttime`),
  KEY `refcode` (`refcode`),
  KEY `library` (`refcode`),
  CONSTRAINT `episode` FOREIGN KEY (`callsign`, `programname`, `date`, `starttime`) REFERENCES `episode` (`callsign`, `programname`, `date`, `starttime`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `library` FOREIGN KEY (`refcode`) REFERENCES `library` (`Refcode`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=70522 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=435446 DEFAULT CHARSET=utf8 COMMENT='Contains records of switch activity';
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-11-11 14:15:08
