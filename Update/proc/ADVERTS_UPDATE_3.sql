ALTER TABLE radio_show_promos MODIFY COLUMN showDay TINYINT(1) UNSIGNED NOT NULL, DROP COLUMN showTime, ADD COLUMN showStart VARCHAR(5) NOT NULL, ADD COLUMN showEnd VARCHAR(5) NOT NULL;
