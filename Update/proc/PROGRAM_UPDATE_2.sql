ALTER TABLE `program` DROP FOREIGN KEY `Sponsor`;||
ALTER TABLE `program` DROP COLUMN `SponsId`;||
CREATE TABLE program_sponsors (
   program_ProgramID INT(11) UNSIGNED ZEROFILL NOT NULL,
   adverts_AdId INT(11) NOT NULL,
   PRIMARY KEY (program_ProgramID, adverts_AdId),
   FOREIGN KEY (program_ProgramID) REFERENCES program (ProgramID) ON DELETE CASCADE,
   FOREIGN KEY (adverts_AdId) REFERENCES adverts (AdId) ON DELETE CASCADE
);
