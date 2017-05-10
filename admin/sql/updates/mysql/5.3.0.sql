ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `order_id` VARCHAR(255) DEFAULT NULL;
INSERT INTO `#__hotelreservation_permissions` (`id`, `name`, `code`, `description`) VALUES (28, 'HotelReservation updates', 'updates_hotelreservation', 'HotelReservation updates');
