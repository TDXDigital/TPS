CREATE TABLE `task` ( `taskid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `station` VARCHAR(4) NULL, `command` TEXT NOT NULL, `rawCode` TINYINT NOT NULL, `jobbyid` VARCHAR(45) NULL, PRIMARY KEY (`taskid`), UNIQUE INDEX `taskid_UNIQUE` (`taskid` ASC))||
create table imageBlob (
    `image_id`        int(11)  not null default '0',
    `image_type`      varchar(25) not null default '',
    `image`           blob        not null,
    `image_size`      varchar(25) not null default '',
    `image_ctgy`      varchar(25) not null default '',
    `image_name`      varchar(100) not null default ''
)||
CREATE TABLE `notification` ( `notificationid` INT UNSIGNED NOT NULL AUTO_INCREMENT, `message` MEDIUMTEXT NOT NULL, `acknowledge` TINYINT NOT NULL DEFAULT 0, `time` DATETIME NOT NULL, `class` ENUM('broadcast', 'notice', 'alert', 'task', 'maintenance', 'message', 'reminder', 'html', 'script') NOT NULL DEFAULT 'notice', `expires` DATETIME NULL DEFAULT NULL, `userName` TEXT NULL, `memberId` INT(11) NULL, `permissionRequired` INT(10) UNSIGNED NOT NULL DEFAULT 1, `origin` ENUM('user', 'system', 'task') NOT NULL DEFAULT 'system', `severity` ENUM('critical', 'urgent', 'high', 'medium', 'low', 'info', 'verbose') NULL DEFAULT 'info',
  `tag` TEXT NULL DEFAULT NULL,
  `image` INT(11) NULL,
  `acknowledged` DATETIME NULL,
  `station` VARCHAR(4) NOT NULL DEFAULT '*';
  PRIMARY KEY (`notificationid`),
  INDEX `perm_idx` (`permissionRequired` ASC))