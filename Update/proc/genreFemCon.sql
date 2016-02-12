ALTER TABLE `genre` 
ADD COLUMN `femcon` INT(11) NOT NULL DEFAULT 0 
COMMENT 'FemCon instances per hour' AFTER `Station`,
ADD COLUMN `femconperc` FLOAT NOT NULL DEFAULT 0.35 
COMMENT 'Percentages of instances across program' AFTER `femcon`,
ADD COLUMN `FcType` INT(2) NOT NULL DEFAULT 1 
COMMENT 'FemConPercentage, 1=percentage 0=numeric' AFTER `femconperc`,
ADD COLUMN `colorPrimary` VARCHAR(8) COMMENT 'String value of hex color' AFTER `FcType`;
/* Change the delimiter so we can use ";" within the CREATE TRIGGER */
DELIMITER $$
CREATE TRIGGER defaultColor
BEFORE INSERT ON `genre`
FOR EACH ROW
BEGIN
	IF (NEW.colorPrimary IS NULL) THEN
		SET NEW.colorPrimary = CONCAT('#',string(CONV(ROUND((RAND() * (16777215-1118481))+1118481), 10, 16)));
	END IF;
END$$
/* This is now "END$$" not "END;" */

/* Reset the delimiter back to ";" */
DELIMITER ;