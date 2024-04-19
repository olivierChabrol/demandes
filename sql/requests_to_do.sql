-- Add a new column 'guest_mail' to the 'dmission_order' table. The column will be placed after the 'guest_name' column. The data type of the column is VARCHAR(250) and it can contain NULL values.
ALTER TABLE `dmission_order` ADD `guest_mail` VARCHAR(250) NULL AFTER `guest_name`;

-- Update the 'guest_mail' column in the 'dmission_order' table with the values from the 'guest_age' column.
UPDATE `dmission_order` SET `guest_mail`=`guest_age`;

-- Add a new column 'guest_birthdate' to the 'dmission_order' table. The column will be placed after the 'guest_mail' column. The data type of the column is DATE and it can contain NULL values.
ALTER TABLE `dmission_order` ADD `guest_birthdate` DATE NULL AFTER `guest_mail`;

-- Add a new column 'guest_phone_number' to the 'dmission_order' table. The column will be placed after the 'guest_birthdate' column. The data type of the column is VARCHAR(15) and it can contain NULL values.
ALTER TABLE `dmission_order` ADD `guest_phone_number` VARCHAR(15) NULL AFTER `guest_birthdate`;

-- Update the 'date_start' column in the 'tincidents' table with the date part of the 'date_start' column from the 'dmission_order' table where the 'id' of the 'tincidents' table matches the 'incident_id' of the 'dmission_order' table.
UPDATE `tincidents`
    JOIN `dmission_order` ON `tincidents`.id = `dmission_order`.`incident_id`
SET `tincidents`.`date_start` = DATE(`dmission_order`.`date_start`);

-- Insert a new row into the 'tcategory' table. The 'id' column is set to NULL (which will be replaced by an auto-incremented value), the 'number' column is set to '4', the 'name' column is set to 'Invitation', and the 'service', 'technician', and 'technician_group' columns are set to '0'.
INSERT INTO `tcategory` (`id`, `number`, `name`, `service`, `technician`, `technician_group`) VALUES (NULL, '4', 'Invitation', '0', '0', '0');

-- Update the 'category' column in the 'tincidents' table to '4' where the 'id' of the 'tincidents' table matches the 'incident_id' of the 'dmission_order' table and the 'om_for_guest' column of the 'dmission_order' table is '1'.
UPDATE `tincidents`
JOIN `dmission_order` ON `tincidents`.id = `dmission_order`.`incident_id`
SET `tincidents`.`category` = 4
WHERE `dmission_order`.`om_for_guest`=1;