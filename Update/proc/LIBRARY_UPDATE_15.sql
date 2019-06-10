CREATE TABLE library_hometowns (
  library_RefCode BIGINT(20) UNSIGNED NOT NULL,
  hometown_id MEDIUMINT NOT NULL,
  PRIMARY KEY (library_RefCode, hometown_id),
  FOREIGN KEY (library_RefCode) REFERENCES library (RefCode) ON DELETE CASCADE,
  FOREIGN KEY (hometown_id) REFERENCES hometowns (id) ON DELETE CASCADE
);
