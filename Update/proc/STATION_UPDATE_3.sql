ALTER TABLE station ADD COLUMN hostProbationPeriodDays SMALLINT UNSIGNED NOT NULL DEFAULT 0, ADD COLUMN hostProbationWeightMultiplier FLOAT NOT NULL DEFAULT 1.0;
