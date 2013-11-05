SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `ckxu` DEFAULT CHARACTER SET utf8 ;
USE `ckxu` ;

-- -----------------------------------------------------
-- Table `ckxu`.`adverts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`adverts` (
  `AdId` INT(11) NOT NULL AUTO_INCREMENT ,
  `Category` INT(11) NOT NULL ,
  `Length` INT(11) NULL DEFAULT '0' ,
  `EndDate` DATE NULL DEFAULT '9999-12-31' ,
  `StartDate` DATE NULL DEFAULT '0001-01-01' ,
  `Playcount` BIGINT(20) UNSIGNED NULL DEFAULT '0' ,
  `AdName` VARCHAR(45) NOT NULL ,
  `Rotation` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `Active` INT(1) UNSIGNED NOT NULL DEFAULT '1' ,
  `Friend` INT(1) NOT NULL DEFAULT '0' ,
  `Language` VARCHAR(45) NOT NULL DEFAULT 'English' ,
  `XREF` INT(11) NULL DEFAULT NULL ,
  `Limit` INT(11) NULL DEFAULT NULL ,
  PRIMARY KEY (`AdId`, `Category`) )
ENGINE = InnoDB
AUTO_INCREMENT = 77
DEFAULT CHARACTER SET = utf8
COMMENT = 'contains information on available Ads and Friends';


-- -----------------------------------------------------
-- Table `ckxu`.`adrotation`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`adrotation` (
  `RotationNum` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `startTime` TIME NOT NULL DEFAULT '00:00:00' ,
  `endTime` TIME NOT NULL DEFAULT '24:00:00' ,
  `BlockLimit` INT(11) NOT NULL DEFAULT '1' ,
  `AdId` INT(11) NOT NULL ,
  `HourlyLimit` INT(11) UNSIGNED NOT NULL DEFAULT '1' ,
  PRIMARY KEY (`RotationNum`) ,
  UNIQUE INDEX `RotationNum_UNIQUE` (`RotationNum` ASC) ,
  INDEX `AdRef` (`AdId` ASC) ,
  CONSTRAINT `AdRef`
    FOREIGN KEY (`AdId` )
    REFERENCES `ckxu`.`adverts` (`AdId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 47
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains information pertaining to Ads required to be played';


-- -----------------------------------------------------
-- Table `ckxu`.`addays`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`addays` (
  `AdIdRef` INT(11) UNSIGNED NOT NULL ,
  `Day` ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL ,
  `AdDayId` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`AdDayId`) ,
  INDEX `REF` (`AdIdRef` ASC) ,
  CONSTRAINT `REF`
    FOREIGN KEY (`AdIdRef` )
    REFERENCES `ckxu`.`adrotation` (`RotationNum` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 142
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`dj`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`dj` (
  `Alias` VARCHAR(50) NOT NULL ,
  `djname` VARCHAR(45) NULL DEFAULT NULL ,
  `active` INT(11) NULL DEFAULT NULL ,
  `years` INT(11) NULL DEFAULT NULL ,
  `Username` VARCHAR(10) NULL DEFAULT NULL ,
  `password` VARCHAR(10) NULL DEFAULT NULL ,
  PRIMARY KEY (`Alias`) ,
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains the DJ information';


-- -----------------------------------------------------
-- Table `ckxu`.`station`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`station` (
  `callsign` VARCHAR(4) NOT NULL ,
  `stationname` VARCHAR(45) NOT NULL ,
  `Designation` VARCHAR(45) NULL DEFAULT NULL ,
  `frequency` VARCHAR(10) NULL DEFAULT NULL ,
  `website` VARCHAR(45) NULL DEFAULT NULL ,
  `address` VARCHAR(100) NULL DEFAULT NULL ,
  `boothphone` VARCHAR(45) NULL DEFAULT NULL ,
  `directorphone` VARCHAR(45) NULL DEFAULT NULL ,
  `ST_DefaultSort` VARCHAR(45) NOT NULL DEFAULT 'ASC' ,
  `ST_PLLG` INT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Playlist Group Live Setting' ,
  `ST_ForceComposer` INT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `ST_ForceArtist` INT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `ST_ForceAlbum` INT(1) UNSIGNED NOT NULL DEFAULT '0' ,
  `ST_ColorFail` VARCHAR(45) NOT NULL DEFAULT '#FFFF00' ,
  `ST_ColorPass` VARCHAR(45) NOT NULL DEFAULT '#90EE90' ,
  `ST_PLRG` INT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Playlist Live Grouping for Reports' ,
  `ST_DispCount` INT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Display Counters on Screen' ,
  `ST_ColorNote` VARCHAR(45) NOT NULL DEFAULT '#ADD8E6' ,
  `managerphone` VARCHAR(45) NULL DEFAULT NULL ,
  `ST_ADSH` INT(10) UNSIGNED NOT NULL DEFAULT '1' ,
  `ST_PSAH` INT(10) UNSIGNED NOT NULL DEFAULT '2' ,
  PRIMARY KEY (`callsign`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains information about stations';


-- -----------------------------------------------------
-- Table `ckxu`.`program`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`program` (
  `programname` VARCHAR(75) NOT NULL ,
  `callsign` VARCHAR(4) NOT NULL ,
  `length` INT(11) NULL DEFAULT NULL ,
  `syndicatesource` VARCHAR(45) NULL DEFAULT NULL ,
  `genre` VARCHAR(20) NULL DEFAULT 'Eclectic' ,
  `active` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' ,
  `Airtime` TIME NULL DEFAULT NULL ,
  `CCX` INT(11) NOT NULL DEFAULT '-1' ,
  `PLX` INT(11) NOT NULL DEFAULT '-1' ,
  `HitLimit` INT(11) NOT NULL DEFAULT '0' ,
  `SponsId` INT(11) NULL DEFAULT NULL ,
  `displayorder` VARCHAR(20) NOT NULL DEFAULT 'desc' ,
  `Theme` INT(11) UNSIGNED NOT NULL DEFAULT '8' ,
  `ProgramID` INT(11) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`programname`, `callsign`) ,
  UNIQUE INDEX `programname_UNIQUE` (`programname` ASC) ,
  UNIQUE INDEX `ProgramID_UNIQUE` (`ProgramID` ASC) ,
  INDEX `callsign` (`callsign` ASC) ,
  INDEX `genre` (`genre` ASC) ,
  INDEX `Sponsor` (`SponsId` ASC) ,
  CONSTRAINT `callsign`
    FOREIGN KEY (`callsign` )
    REFERENCES `ckxu`.`station` (`callsign` ),
  CONSTRAINT `Sponsor`
    FOREIGN KEY (`SponsId` )
    REFERENCES `ckxu`.`adverts` (`AdId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 112
DEFAULT CHARACTER SET = utf8
COMMENT = 'Programs are the actual programs that the Djs perform but no';


-- -----------------------------------------------------
-- Table `ckxu`.`episode`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`episode` (
  `callsign` VARCHAR(4) NOT NULL ,
  `programname` VARCHAR(50) NOT NULL ,
  `date` DATE NOT NULL ,
  `starttime` TIME NOT NULL ,
  `endtime` TIME NULL DEFAULT NULL ,
  `prerecorddate` DATE NULL DEFAULT NULL ,
  `totalspokentime` DOUBLE UNSIGNED NULL DEFAULT NULL ,
  `description` VARCHAR(100) NULL DEFAULT NULL ,
  `Lock` INT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'LOCK - 1 = Finalized; 2 = Admin Lock; 3 = Audit Lock; 0 = No Lock' ,
  `Type` INT(2) NOT NULL DEFAULT '0' COMMENT 'Determines the following: 0=Live, 1=PreRecord, 2=Timeless' ,
  `EpNum` INT(13) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT ,
  `Guests` VARCHAR(45) NULL DEFAULT NULL ,
  `EndStamp` TIMESTAMP NULL DEFAULT NULL ,
  `LastAccess` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`callsign`, `programname`, `date`, `starttime`) ,
  UNIQUE INDEX `EpNum_UNIQUE` (`EpNum` ASC) ,
  INDEX `programref` (`callsign` ASC, `programname` ASC) ,
  INDEX `Program` (`callsign` ASC, `programname` ASC) ,
  CONSTRAINT `Program`
    FOREIGN KEY (`callsign` , `programname` )
    REFERENCES `ckxu`.`program` (`callsign` , `programname` ))
ENGINE = InnoDB
AUTO_INCREMENT = 2401
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`genre`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`genre` (
  `genreid` VARCHAR(20) NOT NULL ,
  `cancon` INT(11) UNSIGNED NOT NULL ,
  `playlist` INT(11) UNSIGNED NOT NULL ,
  `canconperc` FLOAT UNSIGNED NOT NULL DEFAULT '0.4' ,
  `playlistperc` FLOAT UNSIGNED NOT NULL DEFAULT '0.35' ,
  PRIMARY KEY (`genreid`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains the Genre information';


-- -----------------------------------------------------
-- Table `ckxu`.`language`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`language` (
  `callsign` VARCHAR(4) NOT NULL ,
  `programname` VARCHAR(50) NOT NULL ,
  `date` DATE NOT NULL ,
  `starttime` TIME NOT NULL ,
  `songid` INT(50) NOT NULL ,
  `languageid` VARCHAR(45) NOT NULL DEFAULT 'english' ,
  PRIMARY KEY (`callsign`, `programname`, `date`, `starttime`, `songid`, `languageid`) ,
  INDEX `song` (`callsign` ASC, `programname` ASC, `date` ASC, `starttime` ASC, `songid` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`library`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`library` (
  `Refcode` VARCHAR(45) NOT NULL ,
  `Barcode` VARCHAR(45) NULL DEFAULT NULL ,
  `datein` DATE NULL DEFAULT NULL ,
  `dateout` DATE NULL DEFAULT NULL ,
  `year` YEAR NULL DEFAULT NULL ,
  `artist` VARCHAR(100) NULL DEFAULT NULL ,
  `album` VARCHAR(100) NULL DEFAULT NULL ,
  `variousartists` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `format` VARCHAR(45) NULL DEFAULT NULL ,
  `condition` VARCHAR(45) NULL DEFAULT NULL ,
  `label` VARCHAR(45) NULL DEFAULT NULL ,
  `genre` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`Refcode`) ,
  UNIQUE INDEX `Refcode_UNIQUE` (`Refcode` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains Information regarding Archived / Stored information';


-- -----------------------------------------------------
-- Table `ckxu`.`performs`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`performs` (
  `callsign` VARCHAR(4) NOT NULL ,
  `programname` VARCHAR(75) NOT NULL ,
  `Alias` VARCHAR(50) NOT NULL ,
  `STdate` DATETIME NULL DEFAULT '0001-01-01 00:00:00' ,
  `ENdate` DATETIME NULL DEFAULT '9000-01-01 00:00:00' ,
  PRIMARY KEY (`callsign`, `programname`, `Alias`) ,
  INDEX `progref` (`callsign` ASC, `programname` ASC) ,
  INDEX `djref` (`Alias` ASC) ,
  CONSTRAINT `djref`
    FOREIGN KEY (`Alias` )
    REFERENCES `ckxu`.`dj` (`Alias` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `progref`
    FOREIGN KEY (`callsign` , `programname` )
    REFERENCES `ckxu`.`program` (`callsign` , `programname` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`playlist`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`playlist` (
  `number` INT(10) UNSIGNED NOT NULL ,
  `Album` VARCHAR(45) NULL DEFAULT NULL ,
  `Artist` VARCHAR(45) NOT NULL ,
  `Genre` VARCHAR(25) NULL DEFAULT NULL ,
  `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `cancon` ENUM('LC','AC','CC','NC') NOT NULL DEFAULT 'NC' COMMENT 'LC = LocalContent, CC = Canadian Content, NULL = Not CC/LC' ,
  `label` ENUM('IL','SL','ML','LL') NOT NULL DEFAULT 'LL' ,
  `barcode` VARCHAR(45) NULL DEFAULT NULL ,
  `refcode` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`number`) ,
  UNIQUE INDEX `number_UNIQUE` (`number` ASC) ,
  INDEX `library` (`refcode` ASC) ,
  INDEX `libRef` (`refcode` ASC) ,
  CONSTRAINT `libRef`
    FOREIGN KEY (`refcode` )
    REFERENCES `ckxu`.`library` (`Refcode` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`song`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`song` (
  `callsign` VARCHAR(4) NOT NULL ,
  `programname` VARCHAR(50) NOT NULL ,
  `date` DATE NOT NULL ,
  `starttime` TIME NOT NULL ,
  `songid` INT(50) NOT NULL AUTO_INCREMENT ,
  `instrumental` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `time` TIME NULL DEFAULT NULL ,
  `album` VARCHAR(100) NULL DEFAULT NULL ,
  `title` VARCHAR(100) NULL DEFAULT NULL ,
  `artist` VARCHAR(100) NULL DEFAULT NULL ,
  `cancon` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `playlistnumber` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `category` INT(10) UNSIGNED NOT NULL ,
  `hit` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `Spoken` DECIMAL(4,2) NULL DEFAULT NULL ,
  `composer` VARCHAR(100) NULL DEFAULT NULL ,
  `note` VARCHAR(100) NULL DEFAULT NULL ,
  `AdViolationFlag` INT(3) NULL DEFAULT NULL ,
  `barcode` VARCHAR(45) NULL DEFAULT NULL ,
  `refcode` VARCHAR(45) NULL DEFAULT NULL ,
  `Timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Resords the last access time for the record' ,
  PRIMARY KEY (`songid`, `callsign`, `programname`, `date`, `starttime`) ,
  INDEX `episode` (`callsign` ASC, `programname` ASC, `date` ASC, `starttime` ASC) ,
  INDEX `refcode` (`refcode` ASC) ,
  INDEX `library` (`refcode` ASC) ,
  CONSTRAINT `episode`
    FOREIGN KEY (`callsign` , `programname` , `date` , `starttime` )
    REFERENCES `ckxu`.`episode` (`callsign` , `programname` , `date` , `starttime` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `library`
    FOREIGN KEY (`refcode` )
    REFERENCES `ckxu`.`library` (`Refcode` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 51241
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`promptlog`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`promptlog` (
  `idPromptLog` INT(13) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `EpNum` INT(13) UNSIGNED ZEROFILL NULL DEFAULT NULL ,
  `AdNum` INT(11) NULL DEFAULT NULL ,
  `PromptTime` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `PlayTime` DATETIME NULL DEFAULT NULL ,
  `SongNum` INT(50) NULL DEFAULT NULL ,
  PRIMARY KEY (`idPromptLog`) ,
  UNIQUE INDEX `idPromptLog_UNIQUE` (`idPromptLog` ASC) ,
  INDEX `Episode` (`EpNum` ASC) ,
  INDEX `AdRef` (`AdNum` ASC) ,
  INDEX `SongRef` (`SongNum` ASC) ,
  INDEX `Episode Link` (`EpNum` ASC) ,
  INDEX `Song Link` (`SongNum` ASC) ,
  INDEX `Ad Link` (`AdNum` ASC) ,
  INDEX `Song` (`SongNum` ASC) ,
  CONSTRAINT `Song`
    FOREIGN KEY (`SongNum` )
    REFERENCES `ckxu`.`song` (`songid` ))
ENGINE = InnoDB
AUTO_INCREMENT = 1107
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ckxu`.`socan`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`socan` (
  `AuditId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `Enabled` TINYINT(4) NOT NULL DEFAULT '0' ,
  `RQArtist` TINYINT(4) NOT NULL DEFAULT '1' ,
  `RQComposer` TINYINT(4) NOT NULL DEFAULT '1' ,
  `RQAlbum` TINYINT(4) NOT NULL DEFAULT '1' ,
  `start` DATE NOT NULL DEFAULT '0001-01-01' ,
  `end` DATE NOT NULL DEFAULT '9999-12-30' ,
  `RQAfterHr` TINYINT(4) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`AuditId`) ,
  UNIQUE INDEX `AuditId_UNIQUE` (`AuditId` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COMMENT = 'Setings of a Socan or Resound Audit';


-- -----------------------------------------------------
-- Table `ckxu`.`switchstatus`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`switchstatus` (
  `ID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `Bank1` VARCHAR(45) NOT NULL DEFAULT '2' ,
  `Bank2` VARCHAR(45) NOT NULL DEFAULT '2' ,
  `SS` VARCHAR(8) NOT NULL DEFAULT 'S0S,2,2' ,
  `UID` INT(11) NOT NULL DEFAULT '0' ,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`ID`) ,
  UNIQUE INDEX `idSwitchStatus_UNIQUE` (`ID` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 274847
DEFAULT CHARACTER SET = utf8
COMMENT = 'Contains records of switch activity';


-- -----------------------------------------------------
-- Table `ckxu`.`users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ckxu`.`users` (
  `UserId` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `Username` VARCHAR(45) NOT NULL ,
  `Active` INT(1) NOT NULL DEFAULT '0' ,
  `AccessLevel` VARCHAR(1) NULL DEFAULT NULL ,
  `PWDMD5` VARCHAR(100) NOT NULL ,
  `DjAlias` VARCHAR(50) NULL DEFAULT NULL ,
  PRIMARY KEY (`UserId`, `Username`) ,
  UNIQUE INDEX `UserId_UNIQUE` (`UserId` ASC) ,
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC) ,
  INDEX `Alias` (`DjAlias` ASC) ,
  CONSTRAINT `Alias`
    FOREIGN KEY (`DjAlias` )
    REFERENCES `ckxu`.`dj` (`Alias` )
    ON DELETE SET NULL
    ON UPDATE SET NULL)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COMMENT = 'This contains the information that pertains to login of DJ\'s';



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
