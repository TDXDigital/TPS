ALTER TABLE `library` 
ADD COLUMN `governmentCategory` VARCHAR(20) NULL DEFAULT NULL AFTER `playlist_flag`,
ADD COLUMN `scheduleCode` VARCHAR(45) NULL DEFAULT NULL AFTER `governmentCategory`;

