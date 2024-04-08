ALTER TABLE dmission_order
ADD guest_name varchar(250) DEFAULT NULL;

ALTER TABLE dmission_order
ADD guest_age int(11) DEFAULT NULL;

ALTER TABLE dmission_order
ADD guest_labo text DEFAULT NULL;

ALTER TABLE dmission_order
ADD guest_country varchar(250) DEFAULT NULL;

UPDATE tparameters
SET version = '3.2.4-5';