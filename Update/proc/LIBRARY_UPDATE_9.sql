ALTER TABLE `recordlabel` 
ADD COLUMN `parentCompany` BIGINT(20) UNSIGNED NULL AFTER `verified`,
ADD INDEX `parentCompanyFK_idx` (`parentCompany` ASC);
ALTER TABLE `recordlabel` 
ADD CONSTRAINT `parentCompanyFK`
  FOREIGN KEY (`parentCompany`)
  REFERENCES `recordlabel` (`LabelNumber`)
  ON DELETE SET NULL
  ON UPDATE SET NULL;
