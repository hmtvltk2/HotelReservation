ALTER TABLE `#__hotelreservation_hotels` ADD COLUMN `hotel_zipcode` VARCHAR(45) NULL  AFTER `hotel_address` ;

ALTER TABLE `#__hotelreservation_extra_options` ADD COLUMN `map_per_length_of_stay` TINYINT(1) NOT NULL DEFAULT 0  AFTER `status` ;

ALTER TABLE `#__hotelreservation_confirmations_payments` ADD COLUMN `payment_method` VARCHAR(100) NULL  AFTER `currency` ;

ALTER TABLE `#__hotelreservation_offers` ADD COLUMN `featured` TINYINT(1) NOT NULL DEFAULT 0  AFTER `offer_reservation_cost_proc` ;

ALTER TABLE `#__hotelreservation_emails` CHANGE COLUMN `email_type` `email_type` VARCHAR(200) NOT NULL  ;

ALTER TABLE `#__hotelreservation_hotel_informations` ADD COLUMN `pet_info` VARCHAR(100) NULL  AFTER `price_pets` ;



