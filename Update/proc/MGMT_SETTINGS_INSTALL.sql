CREATE TABLE IF NOT EXISTS `managementSettings` (
  `name` VARCHAR(30) NOT NULL,
  `value` VARCHAR(30) NOT NULL,
  UNIQUE KEY `name_UNIQUE` (`name`));
