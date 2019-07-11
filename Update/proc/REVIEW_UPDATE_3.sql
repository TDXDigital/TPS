CREATE TABLE review_hometowns (
  review_id BIGINT(20) UNSIGNED NOT NULL,
  hometown_id MEDIUMINT NOT NULL,
  PRIMARY KEY (review_id, hometown_id),
  FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE,
  FOREIGN KEY (hometown_id) REFERENCES hometowns (id) ON DELETE CASCADE
);||
CREATE TABLE review_recordlabel (
  review_id BIGINT(20) UNSIGNED NOT NULL,
  recordlabel_LabelNumber BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (review_id, recordlabel_LabelNumber),
  FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE,
  FOREIGN KEY (recordlabel_LabelNumber) REFERENCES recordlabel (LabelNumber) ON DELETE CASCADE
);||
CREATE TABLE review_subgenres (
  review_id BIGINT(20) UNSIGNED NOT NULL,
  subgenre_id MEDIUMINT NOT NULL,
  PRIMARY KEY (review_id, subgenre_id),
  FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE,
  FOREIGN KEY (subgenre_id) REFERENCES subgenres (id) ON DELETE CASCADE
);||
CREATE TABLE review_tags (
  review_id BIGINT(20) UNSIGNED NOT NULL,
  tag_id MEDIUMINT NOT NULL,
  PRIMARY KEY (review_id, tag_id),
  FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE
);
