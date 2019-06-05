CREATE TABLE library_recordlabel (
  library_RefCode BIGINT(20) UNSIGNED NOT NULL,
  recordlabel_LabelNumber BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (library_RefCode, recordlabel_LabelNumber),
  FOREIGN KEY (library_RefCode) REFERENCES library (RefCode) ON DELETE CASCADE,
  FOREIGN KEY (recordlabel_LabelNumber) REFERENCES recordlabel (LabelNumber) ON DELETE CASCADE
);
