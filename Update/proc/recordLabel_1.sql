ALTER TABLE `recordlabel` ADD COLUMN `parentCompany` BIGINT(20) UNSIGNED NULL AFTER `verified`, ADD INDEX `parentCompanyFK_idx` (`parentCompany` ASC);
