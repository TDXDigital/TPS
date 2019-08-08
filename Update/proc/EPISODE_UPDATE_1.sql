ALTER TABLE episode ALTER COLUMN LastAccess SET DEFAULT '1970-01-01 00:00:01';||
UPDATE episode SET LastAccess='1970-01-01 00:00:01' WHERE LastAccess < '1000-01-01';||
ALTER TABLE episode DROP FOREIGN KEY Program;||
ALTER TABLE episode ADD CONSTRAINT Program FOREIGN KEY (callsign, programname) REFERENCES program (callsign, programname) ON DELETE CASCADE ON UPDATE CASCADE;

