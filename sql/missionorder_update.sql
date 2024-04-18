ALTER TABLE `dmission_order` ADD `guest_mail` VARCHAR(250) NULL AFTER `guest_name`;
UPDATE `dmission_order` SET `guest_mail`=`guest_age`;
ALTER TABLE `dmission_order` ADD `guest_birthdate` DATE NULL AFTER `guest_mail`;
ALTER TABLE `dmission_order` ADD `guest_phone_number` VARCHAR(15) NULL AFTER `guest_birthdate`;