CREATE SCHEMA IF NOT EXISTS `?` DEFAULT CHARACTER SET utf8 ;
USE `?` ;

CREATE TABLE IF NOT EXISTS `?`.`clients` (
  `ClientNumber` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(80) NOT NULL,
  `CreditLimit` DOUBLE NOT NULL DEFAULT '5000',
  `PaymentTerms` INT(10) NOT NULL DEFAULT '1',
  `Address` TEXT NULL DEFAULT NULL,
  `ContactName` VARCHAR(80) NOT NULL DEFAULT 'Manager / Owner',
  `PhoneNumber` VARCHAR(45) NULL DEFAULT NULL,
  `Status` ENUM('OVL','ACT','EXP','COL','INT','CLO','SUS') NOT NULL DEFAULT 'SUS',
  PRIMARY KEY (`ClientNumber`),
  UNIQUE INDEX `ClientNumber_UNIQUE` (`ClientNumber` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`adverts` (
  `AdId` INT(11) NOT NULL AUTO_INCREMENT,
  `Category` INT(11) NOT NULL,
  `Length` INT(11) NULL DEFAULT '0',
  `EndDate` DATE NULL DEFAULT '9999-12-31',
  `StartDate` DATE NULL DEFAULT '0001-01-01',
  `Playcount` BIGINT(20) NULL DEFAULT '0',
  `AdName` VARCHAR(45) NOT NULL,
  `Rotation` INT(10) UNSIGNED NULL DEFAULT NULL,
  `Active` INT(1) UNSIGNED NOT NULL DEFAULT '1',
  `Friend` INT(1) NOT NULL DEFAULT '0',
  `Language` VARCHAR(45) NOT NULL DEFAULT 'English',
  `XREF` INT(11) NULL DEFAULT NULL,
  `Limit` INT(11) NULL DEFAULT NULL,
  `file` TEXT NULL DEFAULT NULL,
  `ClientID` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
  `last_reset` DATE DEFAULT NULL,
  `last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`AdId`, `Category`),
  INDEX `Client_idx` (`ClientID` ASC),
  CONSTRAINT `Client`
    FOREIGN KEY (`ClientID`)
    REFERENCES `?`.`clients` (`ClientNumber`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 196
DEFAULT CHARACTER SET = utf8
COMMENT = 'contains information on available Ads and Friends';

CREATE TABLE IF NOT EXISTS `?`.`adrotation` (
  `RotationNum` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `startTime` TIME NOT NULL DEFAULT '00:00:00',
  `endTime` TIME NOT NULL DEFAULT '24:00:00',
  `BlockLimit` INT(11) NOT NULL DEFAULT '1',
  `AdId` INT(11) NOT NULL,
  `HourlyLimit` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`RotationNum`),
  UNIQUE INDEX `RotationNum_UNIQUE` (`RotationNum` ASC),
  INDEX `AdRef` (`AdId` ASC),
  CONSTRAINT `AdRef`
    FOREIGN KEY (`AdId`)
    REFERENCES `?`.`adverts` (`AdId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 99
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains information pertaining to Ads required to be played';

CREATE TABLE IF NOT EXISTS `?`.`addays` (
  `AdIdRef` INT(11) UNSIGNED NOT NULL,
  `Day` ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `AdDayId` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`AdDayId`),
  INDEX `REF` (`AdIdRef` ASC),
  CONSTRAINT `REF`
    FOREIGN KEY (`AdIdRef`)
    REFERENCES `?`.`adrotation` (`RotationNum`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 411
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`admininfo` (
  `idAutoFlag` INT(14) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `EventCode` INT(14) UNSIGNED NOT NULL,
  `Creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EpNum` INT(13) UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`idAutoFlag`),
  UNIQUE INDEX `idAutoFlag_UNIQUE` (`idAutoFlag` ASC),
  INDEX `Episode` (`EpNum` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains audit information for programs';

CREATE TABLE IF NOT EXISTS `?`.`airs` (
  `idairs` INT(11) NOT NULL,
  `program` VARCHAR(75) NOT NULL,
  `start_day` INT(7) NOT NULL,
  `end_day` INT(7) NULL DEFAULT NULL,
  `start_time` TIME NULL DEFAULT NULL,
  `end_time` TIME NULL DEFAULT NULL,
  `start_date` DATE NULL DEFAULT NULL,
  `end_date` DATE NULL DEFAULT NULL,
  PRIMARY KEY (`idairs`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Air Times for programs';

CREATE TABLE IF NOT EXISTS `?`.`band_websites` (
  `ID` BIGINT(20) NOT NULL,
  `URL` VARCHAR(45) NOT NULL,
  `Service` VARCHAR(45) NOT NULL DEFAULT 'Website',
  `date_available` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_discontinue` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`ID`, `URL`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'designed to be linked with library entries';

CREATE TABLE IF NOT EXISTS `?`.`device_codes` (
  `device_code` INT(11) NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(45) NOT NULL,
  `Manufacturer` VARCHAR(45) NULL DEFAULT NULL,
  `Code_Description` VARCHAR(45) NULL DEFAULT NULL,
  `Device` VARCHAR(45) NOT NULL,
  `Command` VARCHAR(100) NOT NULL,
  `command_type` INT(4) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`device_code`),
  UNIQUE INDEX `device_UNIQUE` (`device_code` ASC),
  UNIQUE INDEX `Duplicate` (`Device` ASC, `Command` ASC, `command_type` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 24
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(30) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `password` CHAR(128) NOT NULL,
  `salt` CHAR(128) NOT NULL,
  `access` INT(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`dj` (
  `Alias` VARCHAR(50) NOT NULL,
  `djname` VARCHAR(45) NULL DEFAULT NULL,
  `active` INT(11) NULL DEFAULT NULL,
  `years` INT(11) NULL DEFAULT NULL,
  `email_block` INT(2) NULL DEFAULT NULL,
  `member_ref` INT(11) NULL DEFAULT NULL,
  `GUID` VARCHAR(38) NULL DEFAULT NULL,
  PRIMARY KEY (`Alias`),
  INDEX `Member_idx` (`member_ref` ASC),
  CONSTRAINT `Member`
    FOREIGN KEY (`member_ref`)
    REFERENCES `?`.`members` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains the DJ information';

CREATE TABLE IF NOT EXISTS `?`.`emergency_alerts` (
  `AlertId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `EpNum` INT(13) UNSIGNED NOT NULL,
  `Reference` VARCHAR(45) NULL DEFAULT NULL,
  `Source` VARCHAR(45) NULL DEFAULT NULL,
  `Acknowledged` INT(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`AlertId`),
  UNIQUE INDEX `AlertId_UNIQUE` (`AlertId` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`station` (
  `callsign` VARCHAR(4) NOT NULL,
  `stationname` VARCHAR(45) NOT NULL,
  `Designation` VARCHAR(45) NULL DEFAULT NULL,
  `frequency` VARCHAR(10) NULL DEFAULT NULL,
  `website` VARCHAR(45) NULL DEFAULT NULL,
  `address` VARCHAR(100) NULL DEFAULT NULL,
  `boothphone` VARCHAR(45) NULL DEFAULT NULL,
  `directorphone` VARCHAR(45) NULL DEFAULT NULL,
  `ST_DefaultSort` VARCHAR(45) NOT NULL DEFAULT 'ASC',
  `ST_PLLG` INT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Playlist Group Live Setting',
  `ST_ForceComposer` INT(1) UNSIGNED NOT NULL DEFAULT '0',
  `ST_ForceArtist` INT(1) UNSIGNED NOT NULL DEFAULT '0',
  `ST_ForceAlbum` INT(1) UNSIGNED NOT NULL DEFAULT '0',
  `ST_ColorFail` VARCHAR(45) NOT NULL DEFAULT '#FFFF00',
  `ST_ColorPass` VARCHAR(45) NOT NULL DEFAULT '#90EE90',
  `ST_PLRG` INT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Playlist Live Grouping for Reports',
  `ST_DispCount` INT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Display Counters on Screen',
  `ST_ColorNote` VARCHAR(45) NOT NULL DEFAULT '#ADD8E6',
  `managerphone` VARCHAR(45) NULL DEFAULT NULL,
  `ST_ADSH` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  `ST_PSAH` INT(10) UNSIGNED NOT NULL DEFAULT '2',
  `timezone` VARCHAR(45) NOT NULL DEFAULT 'UTC',
  PRIMARY KEY (`callsign`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains information about stations';


CREATE TABLE IF NOT EXISTS `?`.`program` (
  `callsign` VARCHAR(4) NOT NULL,
  `programname` VARCHAR(75) NOT NULL,
  `length` INT(11) NULL DEFAULT NULL,
  `syndicatesource` VARCHAR(45) NULL DEFAULT NULL,
  `genre` VARCHAR(20) NULL DEFAULT 'Eclectic',
  `active` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
  `Airtime` TIME NULL DEFAULT NULL,
  `CCX` INT(11) NOT NULL DEFAULT '-1',
  `PLX` INT(11) NOT NULL DEFAULT '-1',
  `HitLimit` INT(11) NOT NULL DEFAULT '0',
  `SponsId` INT(11) NULL DEFAULT NULL,
  `displayorder` VARCHAR(20) NOT NULL DEFAULT 'desc',
  `Theme` INT(11) UNSIGNED NOT NULL DEFAULT '8',
  `ProgramID` INT(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `Display_Order` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `Reviewable` INT(1) UNSIGNED NOT NULL DEFAULT '1',
  `last_review` DATE NULL DEFAULT NULL,
  PRIMARY KEY (`programname`, `callsign`),
  UNIQUE INDEX `programname_UNIQUE` (`programname` ASC),
  UNIQUE INDEX `ProgramID_UNIQUE` (`ProgramID` ASC),
  INDEX `callsign` (`callsign` ASC),
  INDEX `genre` (`genre` ASC),
  INDEX `Sponsor` (`SponsId` ASC),
  CONSTRAINT `Sponsor`
    FOREIGN KEY (`SponsId`)
    REFERENCES `?`.`adverts` (`AdId`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `callsign`
    FOREIGN KEY (`callsign`)
    REFERENCES `?`.`station` (`callsign`))
ENGINE = InnoDB
AUTO_INCREMENT = 180
DEFAULT CHARACTER SET = utf8
COMMENT = 'Programs are the actual programs that the Djs perform but no';


CREATE TABLE IF NOT EXISTS `?`.`episode` (
  `callsign` VARCHAR(4) NOT NULL,
  `programname` VARCHAR(50) NOT NULL,
  `date` DATE NOT NULL,
  `starttime` TIME NOT NULL,
  `endtime` TIME NULL DEFAULT NULL,
  `prerecorddate` DATE NULL DEFAULT NULL,
  `totalspokentime` DOUBLE UNSIGNED NULL DEFAULT NULL,
  `description` VARCHAR(100) NULL DEFAULT NULL,
  `Lock` INT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'LOCK - 1 = Finalized. 2 = Admin Lock 3 = Audit Lock. 0 = No Lock',
  `Type` INT(2) NOT NULL DEFAULT '0' COMMENT 'Determines the following: 0=Live, 1=PreRecord, 2=Timeless',
  `EpNum` INT(13) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `Guests` VARCHAR(45) NULL DEFAULT NULL,
  `EndStamp` TIMESTAMP NULL DEFAULT NULL,
  `LastAccess` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `score` DOUBLE NULL DEFAULT NULL,
  `Reviewed_Date` DATE NULL DEFAULT NULL,
  `created` TIMESTAMP NULL DEFAULT NULL,
  `IP_Created` VARCHAR(45) NULL DEFAULT NULL,
  `IP_last_access` VARCHAR(45) NULL DEFAULT NULL,
  `IP_Finalized` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`callsign`, `programname`, `date`, `starttime`),
  UNIQUE INDEX `EpNum_UNIQUE` (`EpNum` ASC),
  INDEX `programref` (`callsign` ASC, `programname` ASC),
  INDEX `Program` (`callsign` ASC, `programname` ASC),
  CONSTRAINT `Program`
    FOREIGN KEY (`callsign` , `programname`)
    REFERENCES `?`.`program` (`callsign` , `programname`))
ENGINE = InnoDB
AUTO_INCREMENT = 6137
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`eventcode` (
  `id` INT(14) UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idnew_table_UNIQUE` (`id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`genre` (
  `genreid` VARCHAR(20) NOT NULL,
  `cancon` INT(11) UNSIGNED NOT NULL,
  `playlist` INT(11) UNSIGNED NOT NULL,
  `canconperc` FLOAT UNSIGNED NOT NULL DEFAULT '0.4',
  `playlistperc` FLOAT UNSIGNED NOT NULL DEFAULT '0.35',
  `UID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `PlType` INT(2) UNSIGNED NOT NULL DEFAULT '1',
  `CCType` INT(2) UNSIGNED NOT NULL DEFAULT '1',
  `Station` VARCHAR(4) NOT NULL,
  PRIMARY KEY (`genreid`),
  UNIQUE INDEX `UID_UNIQUE` (`UID` ASC),
  INDEX `STATION_idx` (`Station` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains the Genre information';

CREATE TABLE IF NOT EXISTS `?`.`hardware` (
  `hardwareid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `barcode` VARCHAR(45) NULL DEFAULT NULL,
  `ipv4_address` VARCHAR(16) NOT NULL,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL,
  `hardware_type` INT(5) NOT NULL DEFAULT '0',
  `room` INT(10) NULL DEFAULT NULL,
  `station` VARCHAR(4) NULL DEFAULT NULL,
  `in_service` INT(2) UNSIGNED NOT NULL DEFAULT '1',
  `device_code` VARCHAR(45) NULL DEFAULT NULL,
  `port` INT(8) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`hardwareid`),
  UNIQUE INDEX `hardwareid_UNIQUE` (`hardwareid` ASC),
  INDEX `Device_idx` (`device_code` ASC),
  CONSTRAINT `Device`
    FOREIGN KEY (`device_code`)
    REFERENCES `?`.`device_codes` (`Device`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`hardwareevents` (
  `idHardwareEvents` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Event` VARCHAR(45) NULL DEFAULT NULL,
  `Port` VARCHAR(45) NULL DEFAULT NULL,
  `SystemID` VARCHAR(45) NULL DEFAULT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idHardwareEvents`),
  UNIQUE INDEX `idHardwareEvents_UNIQUE` (`idHardwareEvents` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 38
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`language` (
  `callsign` VARCHAR(4) NOT NULL,
  `programname` VARCHAR(50) NOT NULL,
  `date` DATE NOT NULL,
  `starttime` TIME NOT NULL,
  `songid` INT(50) NOT NULL,
  `languageid` VARCHAR(45) NOT NULL DEFAULT 'english',
  PRIMARY KEY (`callsign`, `programname`, `date`, `starttime`, `songid`, `languageid`),
  INDEX `song` (`callsign` ASC, `programname` ASC, `date` ASC, `starttime` ASC, `songid` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`recordlabel` (
  `LabelNumber` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(100) NOT NULL DEFAULT 'DefaultLabel',
  `Location` INT(20) NULL DEFAULT NULL,
  `Size` INT(4) NOT NULL DEFAULT '1',
  `name_alias_duplicate` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `verified` ENUM('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`LabelNumber`, `Name`),
  UNIQUE INDEX `idRecordLabel_UNIQUE` (`LabelNumber` ASC),
  UNIQUE INDEX `Name` (`Name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 328032
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`library` (
  `Barcode` VARCHAR(45) NULL DEFAULT NULL,
  `datein` DATE NULL DEFAULT NULL,
  `dateout` DATE NULL DEFAULT NULL,
  `year` YEAR NULL DEFAULT NULL,
  `artist` VARCHAR(100) NOT NULL,
  `album` VARCHAR(100) NOT NULL,
  `variousartists` INT(10) UNSIGNED NULL DEFAULT NULL,
  `format` VARCHAR(45) NULL DEFAULT NULL,
  `condition` VARCHAR(45) NULL DEFAULT NULL,
  `genre` VARCHAR(45) NULL DEFAULT NULL,
  `status` INT(3) NULL DEFAULT NULL,
  `labelid` BIGINT(20) UNSIGNED NOT NULL,
  `playlistid` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
  `Locale` ENUM('Local','County','State','Province','Country','International') NOT NULL DEFAULT 'International',
  `CanCon` INT(1) UNSIGNED NOT NULL DEFAULT '0',
  `RefCode` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `HasPL` ENUM('TRUE','FALSE','NA','PRE') NOT NULL DEFAULT 'NA',
  `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `release_date` DATE NULL DEFAULT NULL,
  `note` VARCHAR(120) NULL DEFAULT NULL,
  `playlist_flag` ENUM('PENDING','FALSE','COMPLETE') NOT NULL DEFAULT 'FALSE',
  PRIMARY KEY (`RefCode`),
  UNIQUE INDEX `RefCode_UNIQUE` (`RefCode` ASC),
  UNIQUE INDEX `library_id` (`album` ASC, `artist` ASC, `datein` ASC),
  UNIQUE INDEX `Barcode_UNIQUE` (`Barcode` ASC),
  INDEX `Playlist_idx` (`playlistid` ASC),
  INDEX `Label_idx` (`labelid` ASC),
  CONSTRAINT `Record`
    FOREIGN KEY (`labelid`)
    REFERENCES `?`.`recordlabel` (`LabelNumber`)
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 580362
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains Information regarding Archived / Stored information';

CREATE TABLE IF NOT EXISTS `?`.`playlist` (
  `ZoneCode` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Second Generation Playlist Numbering (ie. D0043 is valid as well as DPL_SERV002)',
  `ZoneNumber` INT(10) NULL DEFAULT NULL,
  `SmallCode` INT(10) UNSIGNED NOT NULL COMMENT 'IE Version 1 (numerical Only)',
  `Activate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Expire` TIMESTAMP NULL DEFAULT NULL,
  `PlaylistId` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `RefCode` BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`PlaylistId`),
  UNIQUE INDEX `playlistid_UNIQUE` (`PlaylistId` ASC),
  INDEX `Library_idx` (`RefCode` ASC),
  CONSTRAINT `Lib_Ref`
    FOREIGN KEY (`RefCode`)
    REFERENCES `?`.`library` (`RefCode`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`login_attempts` (
  `user_id` INT(11) NOT NULL,
  `time` VARCHAR(30) NOT NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`new_table` (
  `AuditId` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `IP_Match` INT(1) UNSIGNED NULL DEFAULT NULL,
  `Pl_Pass` DOUBLE NULL DEFAULT NULL,
  `CC_Pass` DOUBLE NULL DEFAULT NULL,
  `EpNum` INT(13) UNSIGNED NULL DEFAULT NULL,
  `Spoken` DOUBLE NULL DEFAULT NULL,
  `Hit_Pass` DOUBLE NULL DEFAULT NULL,
  `Advert_Pass` DOUBLE NULL DEFAULT NULL,
  `PSA_Pass` DOUBLE NULL DEFAULT NULL,
  `Time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AuditId`),
  UNIQUE INDEX `AuditId_UNIQUE` (`AuditId` ASC),
  INDEX `EPNUM_idx` (`EpNum` ASC),
  CONSTRAINT `EPNUM`
    FOREIGN KEY (`EpNum`)
    REFERENCES `?`.`episode` (`EpNum`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`on_air` (
  `instance` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `SourceAir` INT(4) UNSIGNED NOT NULL,
  `SourceRecord` INT(4) UNSIGNED NOT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`instance`),
  UNIQUE INDEX `instance_UNIQUE` (`instance` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1483612
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`performs` (
  `callsign` VARCHAR(4) NOT NULL,
  `programname` VARCHAR(75) NOT NULL,
  `Alias` VARCHAR(50) NOT NULL,
  `STdate` DATETIME NULL DEFAULT '0001-01-01 00:00:00',
  `ENdate` DATETIME NULL DEFAULT '9000-01-01 00:00:00',
  PRIMARY KEY (`callsign`, `programname`, `Alias`),
  INDEX `progref` (`callsign` ASC, `programname` ASC),
  INDEX `djref` (`Alias` ASC),
  CONSTRAINT `djref`
    FOREIGN KEY (`Alias`)
    REFERENCES `?`.`dj` (`Alias`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `progref`
    FOREIGN KEY (`callsign` , `programname`)
    REFERENCES `?`.`program` (`callsign` , `programname`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`permissions` (
  `idpermissions` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `access` INT(1) NULL DEFAULT NULL,
  `Station_Settings_View` INT(1) NULL DEFAULT '1',
  `Station_Settings_Edit` INT(1) NULL DEFAULT '0',
  `Callsign` VARCHAR(4) NULL DEFAULT NULL,
  `Playsheet_Create` INT(1) NULL DEFAULT '1',
  `Playsheet_View` INT(1) NULL DEFAULT '1',
  `Playsheet_Edit` INT(1) NULL DEFAULT '1',
  `Advert_View` INT(1) NULL DEFAULT '0',
  `Advert_Edit` INT(1) NULL DEFAULT '0',
  `Advert_Create` INT(1) NULL DEFAULT '0',
  `Audit_View` INT(1) NULL DEFAULT '1',
  `Member_Create` INT(1) NULL DEFAULT '0',
  `Member_View` INT(1) NULL DEFAULT '0',
  `Member_Edit` INT(1) NULL DEFAULT '0',
  `Program_Create` INT(1) NULL DEFAULT '0',
  `Program_View` INT(1) NULL DEFAULT '0',
  `Program_Edit` INT(1) NULL DEFAULT '0',
  `Genre_View` INT(1) NULL DEFAULT '0',
  `Genre_Create` INT(1) NULL DEFAULT '0',
  `Genre_Edit` INT(1) NULL DEFAULT '0',
  `Library_View` INT(1) NULL DEFAULT '0',
  `Library_Edit` INT(1) NULL DEFAULT '0',
  `Library_Create` INT(1) NULL DEFAULT '0',
  PRIMARY KEY (`idpermissions`),
  UNIQUE INDEX `idpermissions_UNIQUE` (`idpermissions` ASC),
  UNIQUE INDEX `Station_Unique_access` (`access` ASC, `Callsign` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`song` (
  `songid` INT(50) NOT NULL AUTO_INCREMENT,
  `callsign` VARCHAR(4) NOT NULL,
  `programname` VARCHAR(50) NOT NULL,
  `date` DATE NOT NULL,
  `starttime` TIME NOT NULL,
  `instrumental` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `time` TIME NULL DEFAULT NULL,
  `album` VARCHAR(100) NULL DEFAULT NULL,
  `title` VARCHAR(100) NULL DEFAULT NULL,
  `artist` VARCHAR(100) NULL DEFAULT NULL,
  `cancon` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `playlistnumber` INT(10) UNSIGNED NULL DEFAULT NULL,
  `category` INT(10) UNSIGNED NOT NULL,
  `hit` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `Spoken` DECIMAL(4,2) NULL DEFAULT NULL,
  `composer` VARCHAR(100) NULL DEFAULT NULL,
  `note` VARCHAR(100) NULL DEFAULT NULL,
  `AdViolationFlag` INT(3) NULL DEFAULT NULL,
  `barcode` VARCHAR(45) NULL DEFAULT NULL,
  `Timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Records the last access time for the record',
  `RefCode` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`songid`, `callsign`, `programname`, `date`, `starttime`),
  INDEX `episode` (`callsign` ASC, `programname` ASC, `date` ASC, `starttime` ASC),
  INDEX `library_idx` (`RefCode` ASC),
  CONSTRAINT `episode`
    FOREIGN KEY (`callsign` , `programname` , `date` , `starttime`)
    REFERENCES `?`.`episode` (`callsign` , `programname` , `date` , `starttime`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `library`
    FOREIGN KEY (`RefCode`)
    REFERENCES `?`.`library` (`RefCode`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 131113
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`promptlog` (
  `idPromptLog` INT(13) UNSIGNED NOT NULL AUTO_INCREMENT,
  `EpNum` INT(13) UNSIGNED ZEROFILL NULL DEFAULT NULL,
  `AdNum` INT(11) NULL DEFAULT NULL,
  `PromptTime` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `PlayTime` DATETIME NULL DEFAULT NULL,
  `SongNum` INT(50) NULL DEFAULT NULL,
  PRIMARY KEY (`idPromptLog`),
  UNIQUE INDEX `idPromptLog_UNIQUE` (`idPromptLog` ASC),
  INDEX `Episode` (`EpNum` ASC),
  INDEX `AdRef` (`AdNum` ASC),
  INDEX `SongRef` (`SongNum` ASC),
  INDEX `Episode Link` (`EpNum` ASC),
  INDEX `Song Link` (`SongNum` ASC),
  INDEX `Ad Link` (`AdNum` ASC),
  INDEX `Song` (`SongNum` ASC),
  CONSTRAINT `Song`
    FOREIGN KEY (`SongNum`)
    REFERENCES `?`.`song` (`songid`))
ENGINE = InnoDB
AUTO_INCREMENT = 26995
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`rds` (
  `rds_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rds_status` VARCHAR(10) NULL DEFAULT NULL,
  `value` VARCHAR(100) NULL DEFAULT NULL,
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `server` VARCHAR(100) NULL DEFAULT NULL,
  `type` FLOAT NULL DEFAULT NULL,
  PRIMARY KEY (`rds_id`),
  UNIQUE INDEX `RDS_id_UNIQUE` (`rds_id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 467383
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`replicates` (
  `idReplicates` INT(10) UNSIGNED NOT NULL,
  `ReferenceEpisode` INT(13) UNSIGNED ZEROFILL NOT NULL,
  `OverrideTimestamp` DATETIME NOT NULL,
  PRIMARY KEY (`idReplicates`),
  INDEX `Episode_idx` (`ReferenceEpisode` ASC),
  CONSTRAINT `episodenum`
    FOREIGN KEY (`ReferenceEpisode`)
    REFERENCES `?`.`episode` (`EpNum`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`socan` (
  `AuditId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Enabled` TINYINT(4) NOT NULL DEFAULT '0',
  `RQArtist` TINYINT(4) NOT NULL DEFAULT '1',
  `RQComposer` TINYINT(4) NOT NULL DEFAULT '1',
  `RQAlbum` TINYINT(4) NOT NULL DEFAULT '1',
  `start` DATE NOT NULL DEFAULT '0001-01-01',
  `end` DATE NOT NULL DEFAULT '9999-12-30',
  `RQAfterHr` TINYINT(4) NOT NULL DEFAULT '0',
  `Timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ShowPrompt` TINYINT(4) NOT NULL DEFAULT '1',
  `StationID` VARCHAR(4) NOT NULL,
  `Description` TEXT NULL DEFAULT NULL,
  `Statement` VARCHAR(254) NOT NULL DEFAULT 'NOTICE: An audit is in effect at this station. Accurate reporting is vital at this time, extra information may be required. See your program director for more information',
  PRIMARY KEY (`AuditId`),
  UNIQUE INDEX `AuditId_UNIQUE` (`AuditId` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8
COMMENT = 'Setings of a Socan or Resound Audit';

CREATE TABLE IF NOT EXISTS `?`.`switchstatus` (
  `ID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Bank1` VARCHAR(45) NOT NULL DEFAULT '2',
  `Bank2` VARCHAR(45) NOT NULL DEFAULT '2',
  `SS` VARCHAR(8) NOT NULL DEFAULT 'S0S,2,2',
  `UID` INT(11) NOT NULL DEFAULT '0',
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE INDEX `idSwitchStatus_UNIQUE` (`ID` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1949048
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains records of switch activity';

CREATE TABLE IF NOT EXISTS `?`.`system` (
  `sys_version_id` INT(11) NOT NULL DEFAULT '0',
  `db_version` INT(11) NOT NULL DEFAULT '1',
  `db_revision` INT(11) NOT NULL DEFAULT '1',
  `install_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sys_version_id`),
  UNIQUE INDEX `DB_VERSION_UNIQUE` (`sys_version_id` ASC),
  UNIQUE INDEX `Unique_Instance` (`sys_version_id` ASC, `db_version` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


CREATE TABLE IF NOT EXISTS `?`.`trafficaudit` (
  `idTrafficAudit` INT(11) NOT NULL AUTO_INCREMENT,
  `songid` INT(50) NOT NULL,
  `advertid` INT(11) NOT NULL,
  `StartTime` TIME NULL DEFAULT NULL,
  `EndTime` TIME NULL DEFAULT NULL,
  `complete` BIT(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`idTrafficAudit`),
  INDEX `song` (`songid` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 2961
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `?`.`users` (
  `UserId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(45) NOT NULL,
  `Active` INT(1) NOT NULL DEFAULT '0',
  `AccessLevel` VARCHAR(1) NULL DEFAULT NULL,
  `PWDMD5` VARCHAR(100) NOT NULL,
  `DjAlias` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`UserId`, `Username`),
  UNIQUE INDEX `UserId_UNIQUE` (`UserId` ASC),
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC),
  INDEX `Alias` (`DjAlias` ASC),
  CONSTRAINT `Alias`
    FOREIGN KEY (`DjAlias`)
    REFERENCES `?`.`dj` (`Alias`)
    ON DELETE SET NULL
    ON UPDATE SET NULL)
ENGINE = InnoDB 
DEFAULT CHARACTER SET = utf8
COMMENT = 'This contains the information that pertains to login of DJs';
