ALTER TABLE `eventlog` 
ADD COLUMN `severity` VARCHAR(10) NULL COMMENT '' AFTER `result`;
