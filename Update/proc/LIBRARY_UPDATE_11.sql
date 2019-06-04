CREATE TABLE library_tags (
  library_RefCode BIGINT(20) UNSIGNED NOT NULL,
  tag_id MEDIUMINT NOT NULL,
  PRIMARY KEY (library_RefCode, tag_id),
  FOREIGN KEY (library_RefCode) REFERENCES library (RefCode) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE
);
