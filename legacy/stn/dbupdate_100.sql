ALTER TABLE `ckxu`.`library` 
ADD UNIQUE INDEX `Barcode_UNIQUE` (`Barcode` ASC);

ALTER TABLE `ckxu`.`library` 
DROP INDEX `library_id` ,
ADD UNIQUE INDEX `library_id` (`album` ASC, `artist` ASC, `datein` ASC, `format` ASC);
