ALTER TABLE dparameters
ADD fixed_validators tinyint(5) NOT NULL DEFAULT 0;

UPDATE tparameters
SET version = '3.2.4-4'