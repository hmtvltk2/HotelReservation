ALTER TABLE `#__hotelreservation_extra_options` ADD COLUMN `multiplier` INT(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__hotelreservation_confirmations_extra_options` ADD COLUMN `extra_option_multiplier` INT(3) NULL;
ALTER TABLE `#__hotelreservation_offers` ADD COLUMN `apply_max_nights` INT(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `children_rates_type` INT(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `calendar_availability_type` INT(1) NOT NULL DEFAULT 0;
