INSERT INTO library_recordlabel (SELECT RefCode, labelid FROM library);||
UPDATE library_recordlabel 
  SET recordlabel_LabelNumber=(SELECT LabelNumber FROM recordlabel WHERE Name='Self-Released') 
  WHERE recordlabel_LabelNumber IN (SELECT LabelNumber FROM recordlabel WHERE Name IN ('Self Released', 'Independent', 'SR'));||
UPDATE recordlabel SET name_alias_duplicate=NULL WHERE Name='Self-Released';||
ALTER TABLE `library` DROP FOREIGN KEY `Record`;||
ALTER TABLE `library` DROP INDEX `Label_idx`;||
ALTER TABLE `library` DROP COLUMN `labelid`;||
DELETE FROM recordlabel WHERE Name IN ('Self Released', 'Independent', 'SR');
