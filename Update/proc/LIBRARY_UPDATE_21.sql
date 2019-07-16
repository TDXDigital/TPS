CREATE TABLE tracklists (
    library_RefCode BIGINT(20) UNSIGNED NOT NULL,
    trackNum TINYINT UNSIGNED NOT NULL,
    trackName VARCHAR(40) NOT NULL,
    FOREIGN KEY (library_RefCode) REFERENCES library (RefCode) ON DELETE CASCADE
);
