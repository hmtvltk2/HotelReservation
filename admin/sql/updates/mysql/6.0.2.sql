ALTER TABLE `#__hotelreservation_offers_rates` ADD INDEX `IDX_OFR` (`offer_id` ASC) ; 
ALTER TABLE `#__hotelreservation_offers_rate_prices` ADD INDEX `offrateprice_rateid_idx` (`rate_id`) ; 
ALTER TABLE `#__hotelreservation_rooms` ADD INDEX(`room_id`);
ALTER TABLE `#__hotelreservation_confirmations`  ADD INDEX `confirmations_1_IDX`  (`confirmation_id`,`hotel_id`) ; 
ALTER TABLE `#__hotelreservation_hotel_pictures` ADD INDEX `pictures_1_IDX` (`hotel_id`);
ALTER TABLE `#__hotelreservation_rooms_rate_prices` ADD INDEX `roomrateprices_rateid_idx` (`rate_id`);
ALTER TABLE `#__hotelreservation_language_translations` ADD INDEX `translation_composite_idx` (`type`,`object_id`,`language_tag`);
ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `currency_display` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_hotels` ADD `display_unavailability_message` INT(1) NOT NULL DEFAULT '0' ;