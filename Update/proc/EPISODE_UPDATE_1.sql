ALTER TABLE episode DROP FOREIGN KEY Program;||
ALTER TABLE episode ADD CONSTRAINT Program FOREIGN KEY (callsign, programname) REFERENCES program (callsign, programname) ON DELETE CASCADE ON UPDATE CASCADE;
