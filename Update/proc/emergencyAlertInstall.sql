CREATE TABLE `emergencyalertsettings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `station` VARCHAR(4) NOT NULL,
  `provider` VARCHAR(60) NULL,
  `url` VARCHAR(256) NULL,
  `logo` VARCHAR(256) NULL,
  `locations` TEXT(256) NULL,
  `active` INT UNSIGNED NULL DEFAULT 1,
  `area` GEOMETRY NULL,
  PRIMARY KEY (`id`, `station`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `station_idx` (`station` ASC),
  CONSTRAINT `station`
    FOREIGN KEY (`station`)
    REFERENCES `station` (`callsign`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
COMMENT = 'Stores setting for station Emergency Alert Information';

