ALTER TABLE addays MODIFY COLUMN Day enum('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL;||
UPDATE addays SET Day = 'Sun' WHERE Day = 'Sunday';||
UPDATE addays SET Day = 'Mon' WHERE Day = 'Monday';||
UPDATE addays SET Day = 'Tue' WHERE Day = 'Tuesday';||
UPDATE addays SET Day = 'Wed' WHERE Day = 'Wednesday';||
UPDATE addays SET Day = 'Thu' WHERE Day = 'Thursday';||
UPDATE addays SET Day = 'Fri' WHERE Day = 'Friday';||
UPDATE addays SET Day = 'Sat' WHERE Day = 'Saturday';||
UPDATE addays SET Day = 'Sun' WHERE Day = 'Sunday';||
ALTER TABLE addays MODIFY COLUMN Day enum('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat') NOT NULL;

