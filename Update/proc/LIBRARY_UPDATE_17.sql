CREATE TABLE library_subgenres (
  library_RefCode BIGINT(20) UNSIGNED NOT NULL,
  subgenre_id MEDIUMINT NOT NULL,
  PRIMARY KEY (library_RefCode, subgenre_id),
  FOREIGN KEY (library_RefCode) REFERENCES library (RefCode) ON DELETE CASCADE,
  FOREIGN KEY (subgenre_id) REFERENCES subgenres (id) ON DELETE CASCADE
);
