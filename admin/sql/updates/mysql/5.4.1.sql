ALTER TABLE `#__hotelreservation_confirmations` ADD COLUMN `cancellation_notes` VARCHAR(255) DEFAULT NULL;
INSERT INTO `#__hotelreservation_permissions` (`name`, `code`, `description`) VALUES ('Course/Excursions', 'manage_excursions', 'Manage Course/Excursions');
INSERT INTO `#__hotelreservation_permissions` (`name`, `code`, `description`) VALUES ('Hotel Availability', 'availability_section', 'Hotel Availability');
UPDATE `#__hotelreservation_date_formats` set name='m/d/y',dateFormat='m/d/Y', calendarFormat ='%m/%d/%Y' where id=3;
ALTER TABLE `#__hotelreservation_discounts` ADD COLUMN `excursion_ids` varchar(250) NOT NULL;
INSERT INTO `#__hotelreservation_role_permission_mapping` (`permission_id`, `role_id`) VALUES
(10, 1),
(28, 1),
(29, 1),
(30, 1);