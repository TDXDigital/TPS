ALTER TABLE `library` 
ADD COLUMN `rating` INT(10) NULL COMMENT '' AFTER `scheduleCode`,
ADD COLUMN `sender` VARCHAR(45) NULL COMMENT '' AFTER `rating`,
ADD COLUMN `extrack` VARCHAR(120) NULL COMMENT 'External Tracking ID for National Charting API' AFTER `sender`;
