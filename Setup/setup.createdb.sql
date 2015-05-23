CREATE SCHEMA IF NOT EXISTS `?` DEFAULT CHARACTER SET utf8 ;
USE `?` ;

CREATE TABLE IF NOT EXISTS `?`.`addays` (
  `AdIdRef` int(11) unsigned NOT NULL,
  `Day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `AdDayId` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`AdDayId`),
  KEY `REF` (`AdIdRef`),
  CONSTRAINT `REF` FOREIGN KEY (`AdIdRef`) REFERENCES `adrotation` (`RotationNum`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`admininfo` (
  `idAutoFlag` int(14) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `EventCode` int(14) unsigned NOT NULL,
  `Creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EpNum` int(13) unsigned zerofill NOT NULL,
  PRIMARY KEY (`idAutoFlag`),
  UNIQUE KEY `idAutoFlag_UNIQUE` (`idAutoFlag`),
  KEY `Episode` (`EpNum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains audit information for programs';


CREATE TABLE IF NOT EXISTS `?`.`adrotation` (
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
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8 COMMENT='Contains information pertaining to Ads required to be played';

CREATE TABLE IF NOT EXISTS `?`.`adverts` (
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
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8 COMMENT='contains information on available Ads and Friends';

CREATE TABLE IF NOT EXISTS `?`.`airs` (
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

CREATE TABLE IF NOT EXISTS `?`.`band_websites` (
  `ID` bigint(20) NOT NULL,
  `URL` varchar(45) NOT NULL,
  `Service` varchar(45) NOT NULL DEFAULT 'Website',
  `date_available` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_discontinue` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ID`,`URL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='designed to be linked with library entries';

CREATE TABLE IF NOT EXISTS `?`.`clients` (
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

CREATE TABLE IF NOT EXISTS `?`.`device_codes` (
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`dj` (
  `Alias` varchar(50) NOT NULL,
  `djname` varchar(45) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `years` int(11) DEFAULT NULL,
  `email_block` int(2) DEFAULT NULL,
  `member_ref` int(11) DEFAULT NULL,
  `GUID` varchar(38) DEFAULT NULL,
  PRIMARY KEY (`Alias`),
  KEY `Member_idx` (`member_ref`),
  CONSTRAINT `Member` FOREIGN KEY (`member_ref`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains the DJ information';


CREATE TABLE IF NOT EXISTS `?`.`emergency_alerts` (
  `AlertId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `EpNum` int(13) unsigned NOT NULL,
  `Reference` varchar(45) DEFAULT NULL,
  `Source` varchar(45) DEFAULT NULL,
  `Acknowledged` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`AlertId`),
  UNIQUE KEY `AlertId_UNIQUE` (`AlertId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`episode` (
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
  `score` double DEFAULT NULL,
  `Reviewed_Date` date DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `IP_Created` varchar(45) DEFAULT NULL,
  `IP_last_access` varchar(45) DEFAULT NULL,
  `IP_Finalized` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`callsign`,`programname`,`date`,`starttime`),
  UNIQUE KEY `EpNum_UNIQUE` (`EpNum`),
  KEY `programref` (`callsign`,`programname`),
  KEY `Program` (`callsign`,`programname`),
  CONSTRAINT `Program` FOREIGN KEY (`callsign`, `programname`) REFERENCES `program` (`callsign`, `programname`)
) ENGINE=InnoDB AUTO_INCREMENT=6132 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`eventcode` (
  `id` int(14) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idnew_table_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`genre` (
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='Contains the Genre information';

CREATE TABLE IF NOT EXISTS `?`.`hardware` (
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`hardwareevents` (
  `idHardwareEvents` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Event` varchar(45) DEFAULT NULL,
  `Port` varchar(45) DEFAULT NULL,
  `SystemID` varchar(45) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHardwareEvents`),
  UNIQUE KEY `idHardwareEvents_UNIQUE` (`idHardwareEvents`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `?`.`language` (
  `callsign` varchar(4) NOT NULL,
  `programname` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `songid` int(50) NOT NULL,
  `languageid` varchar(45) NOT NULL DEFAULT 'english',
  PRIMARY KEY (`callsign`,`programname`,`date`,`starttime`,`songid`,`languageid`),
  KEY `song` (`callsign`,`programname`,`date`,`starttime`,`songid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`library` (
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
  `release_date` date DEFAULT NULL,
  `note` varchar(120) DEFAULT NULL,
  `playlist_flag` enum('PENDING','FALSE','COMPLETE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`RefCode`),
  UNIQUE KEY `RefCode_UNIQUE` (`RefCode`),
  UNIQUE KEY `library_id` (`album`,`artist`,`datein`),
  UNIQUE KEY `Barcode_UNIQUE` (`Barcode`),
  KEY `Playlist_idx` (`playlistid`),
  KEY `Label_idx` (`labelid`),
  CONSTRAINT `Playlist` FOREIGN KEY (`playlistid`) REFERENCES `playlist` (`PlaylistId`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `Record` FOREIGN KEY (`labelid`) REFERENCES `recordlabel` (`LabelNumber`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=580362 DEFAULT CHARSET=utf8 COMMENT='Contains Information regarding Archived / Stored information';


CREATE TABLE IF NOT EXISTS `?`.`login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `?`.`members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL,
  `access` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`new_table` (
  `AuditId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `IP_Match` int(1) unsigned DEFAULT NULL,
  `Pl_Pass` double DEFAULT NULL,
  `CC_Pass` double DEFAULT NULL,
  `EpNum` int(13) unsigned DEFAULT NULL,
  `Spoken` double DEFAULT NULL,
  `Hit_Pass` double DEFAULT NULL,
  `Advert_Pass` double DEFAULT NULL,
  `PSA_Pass` double DEFAULT NULL,
  `Time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AuditId`),
  UNIQUE KEY `AuditId_UNIQUE` (`AuditId`),
  KEY `EPNUM_idx` (`EpNum`),
  CONSTRAINT `EPNUM` FOREIGN KEY (`EpNum`) REFERENCES `episode` (`EpNum`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`on_air` (
  `instance` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SourceAir` int(4) unsigned NOT NULL,
  `SourceRecord` int(4) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`instance`),
  UNIQUE KEY `instance_UNIQUE` (`instance`)
) ENGINE=InnoDB AUTO_INCREMENT=1477551 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`performs` (
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

CREATE TABLE IF NOT EXISTS `?`.`permissions` (
  `idpermissions` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access` int(1) DEFAULT NULL,
  `Station_Settings_View` int(1) DEFAULT '1',
  `Station_Settings_Edit` int(1) DEFAULT '0',
  `Callsign` varchar(4) DEFAULT NULL,
  `Playsheet_Create` int(1) DEFAULT '1',
  `Playsheet_View` int(1) DEFAULT '1',
  `Playsheet_Edit` int(1) DEFAULT '1',
  `Advert_View` int(1) DEFAULT '0',
  `Advert_Edit` int(1) DEFAULT '0',
  `Advert_Create` int(1) DEFAULT '0',
  `Audit_View` int(1) DEFAULT '1',
  `Member_Create` int(1) DEFAULT '0',
  `Member_View` int(1) DEFAULT '0',
  `Member_Edit` int(1) DEFAULT '0',
  `Program_Create` int(1) DEFAULT '0',
  `Program_View` int(1) DEFAULT '0',
  `Program_Edit` int(1) DEFAULT '0',
  `Genre_View` int(1) DEFAULT '0',
  `Genre_Create` int(1) DEFAULT '0',
  `Genre_Edit` int(1) DEFAULT '0',
  `Library_View` int(1) DEFAULT '0',
  `Library_Edit` int(1) DEFAULT '0',
  `Library_Create` int(1) DEFAULT '0',
  PRIMARY KEY (`idpermissions`),
  UNIQUE KEY `idpermissions_UNIQUE` (`idpermissions`),
  UNIQUE KEY `Station_Unique_access` (`access`,`Callsign`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`playlist` (
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

CREATE TABLE IF NOT EXISTS `?`.`program` (
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
  `Reviewable` int(1) unsigned NOT NULL DEFAULT '1',
  `last_review` date DEFAULT NULL,
  PRIMARY KEY (`programname`,`callsign`),
  UNIQUE KEY `programname_UNIQUE` (`programname`),
  UNIQUE KEY `ProgramID_UNIQUE` (`ProgramID`),
  KEY `callsign` (`callsign`),
  KEY `genre` (`genre`),
  KEY `Sponsor` (`SponsId`),
  CONSTRAINT `Sponsor` FOREIGN KEY (`SponsId`) REFERENCES `adverts` (`AdId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `callsign` FOREIGN KEY (`callsign`) REFERENCES `station` (`callsign`)
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8 COMMENT='Programs are the actual programs that the Djs perform but no';

CREATE TABLE IF NOT EXISTS `?`.`promptlog` (
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
) ENGINE=InnoDB AUTO_INCREMENT=26991 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`rds` (
  `rds_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rds_status` varchar(10) DEFAULT NULL,
  `value` varchar(100) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` varchar(100) DEFAULT NULL,
  `type` float DEFAULT NULL,
  PRIMARY KEY (`rds_id`),
  UNIQUE KEY `RDS_id_UNIQUE` (`rds_id`)
) ENGINE=InnoDB AUTO_INCREMENT=467179 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`recordlabel` (
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
) ENGINE=InnoDB AUTO_INCREMENT=328032 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`replicates` (
  `idReplicates` int(10) unsigned NOT NULL,
  `ReferenceEpisode` int(13) unsigned zerofill NOT NULL,
  `OverrideTimestamp` datetime NOT NULL,
  PRIMARY KEY (`idReplicates`),
  KEY `Episode_idx` (`ReferenceEpisode`),
  CONSTRAINT `episodenum` FOREIGN KEY (`ReferenceEpisode`) REFERENCES `episode` (`EpNum`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`socan` (
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
  `Statement` varchar(254) NOT NULL DEFAULT 'NOTICE: An audit is in effect at this station. Accurate reporting is vital at this time, extra information may be required. See your program director for more information',
  PRIMARY KEY (`AuditId`),
  UNIQUE KEY `AuditId_UNIQUE` (`AuditId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Setings of a Socan or Resound Audit';

CREATE TABLE IF NOT EXISTS `?`.`song` (
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
) ENGINE=InnoDB AUTO_INCREMENT=130949 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`station` (
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
  `timezone` varchar(45) NOT NULL DEFAULT 'UTC',
  PRIMARY KEY (`callsign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains information about stations';

CREATE TABLE IF NOT EXISTS `?`.`switchstatus` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Bank1` varchar(45) NOT NULL DEFAULT '2',
  `Bank2` varchar(45) NOT NULL DEFAULT '2',
  `SS` varchar(8) NOT NULL DEFAULT 'S0S,2,2',
  `UID` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `idSwitchStatus_UNIQUE` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1942988 DEFAULT CHARSET=utf8 COMMENT='Contains records of switch activity';

CREATE TABLE IF NOT EXISTS `?`.`system` (
  `sys_version_id` int(11) NOT NULL DEFAULT '0',
  `db_version` int(11) NOT NULL DEFAULT '1',
  `db_revision` int(11) NOT NULL DEFAULT '1',
  `install_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sys_version_id`),
  UNIQUE KEY `DB_VERSION_UNIQUE` (`sys_version_id`),
  UNIQUE KEY `Unique_Instance` (`sys_version_id`,`db_version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`trafficaudit` (
  `idTrafficAudit` int(11) NOT NULL AUTO_INCREMENT,
  `songid` int(50) NOT NULL,
  `advertid` int(11) NOT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `complete` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`idTrafficAudit`),
  KEY `song` (`songid`)
) ENGINE=InnoDB AUTO_INCREMENT=2947 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `?`.`users` (
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
