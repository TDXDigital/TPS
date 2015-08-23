ALTER TABLE `library` 
ADD COLUMN `release_date` DATE NULL DEFAULT NULL AFTER `updated`,
ADD COLUMN `note` VARCHAR(120) NULL AFTER `release_date`;

