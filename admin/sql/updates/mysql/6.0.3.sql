ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `apply_search_params` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `capacity_calculation` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_rooms` ADD COLUMN   `beds24_room_id`	int(11) NULL;
ALTER TABLE `#__hotelreservation_confirmations` ADD COLUMN   `beds24_status` TINYINT(1) NOT NULL DEFAULT '0';

