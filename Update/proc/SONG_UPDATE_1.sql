ALTER TABLE song DROP FOREIGN KEY episode;||
ALTER TABLE song ADD CONSTRAINT episode FOREIGN KEY (callsign, programname, date, starttime) REFERENCES episode (callsign, programname, date, starttime) ON DELETE CASCADE ON UPDATE CASCADE;
