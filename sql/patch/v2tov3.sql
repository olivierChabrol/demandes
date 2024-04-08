/* pour passer de SIGED v2 à V3 */
/* (la version de siged est après le - dans lenuméro de version de gestsup) */

ALTER TABLE dmission_order
ADD date_alerte timestamp NOT NULL DEFAULT current_timestamp();

ALTER TABLE dmission_order
MODIFY date_demande timestamp NOT NULL DEFAULT current_timestamp();

ALTER TABLE dpurchase_order
ADD date_alerte timestamp NOT NULL DEFAULT current_timestamp();

ALTER TABLE dpurchase_order
MODIFY date_demande timestamp NOT NULL DEFAULT current_timestamp();

UPDATE tparameters
SET version = '3.2.4-3'