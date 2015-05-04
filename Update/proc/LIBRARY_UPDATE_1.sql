ALTER TABLE `permissions` 
ADD COLUMN `Library_View` INT(1) NULL DEFAULT 0 AFTER `Genre_Edit`,
ADD COLUMN `Library_Edit` INT(1) NULL DEFAULT 0 AFTER `Library_View`,
ADD COLUMN `Library_Create` INT(1) NULL DEFAULT 0 AFTER `Library_Edit`;
