INSERT INTO library_recordlabel (SELECT RefCode, labelid FROM library);||
ALTER TABLE `library` DROP FOREIGN KEY `Record`;||
ALTER TABLE `library` DROP INDEX `Label_idx`;||
ALTER TABLE `library` DROP COLUMN `labelid`;
