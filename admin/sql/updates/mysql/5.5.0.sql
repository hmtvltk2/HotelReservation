INSERT INTO `#__hotelreservation_permissions` (`name`, `code`, `description`) VALUES ('Manage Airport Transfers', 'manage_airport_transfers', 'Manage Airport Transfers');

DELETE FROM `#__hotelreservation_date_formats`;
INSERT INTO `#__hotelreservation_date_formats` (`id`, `name`, `dateFormat`, `calendarFormat`, `defaultDateValue`) VALUES
  (1, 'y-m-d', 'Y-m-d', '%Y-%m-%d', '0000-00-00'),
  (2, 'm/d/y', 'm/d/Y', '%m/%d/%Y', '00/00/0000'),
  (3, 'd-m-y', 'd-m-Y', '%d-%m-%Y', '00-00-0000');


ALTER TABLE `#__hotelreservation_review_questions` ADD COLUMN `ordering` INT(5) NOT NULL AFTER `review_question_nr`;
ALTER TABLE `#__hotelreservation_hotels` ADD COLUMN `hotel_alias` VARCHAR(255) NOT NULL AFTER `hotel_name`;

ALTER TABLE `#__hotelreservation_rooms` CHANGE COLUMN `room_order` `ordering` TINYINT(6) NOT NULL;
ALTER TABLE `#__hotelreservation_offers` CHANGE COLUMN `offer_order` `ordering` TINYINT(6) NOT NULL;
ALTER TABLE `#__hotelreservation_excursions` CHANGE COLUMN `excursion_order` `ordering` TINYINT(6) NOT NULL;
ALTER TABLE `#__hotelreservation_confirmations` ADD COLUMN `language_tag` VARCHAR(25) NOT NULL AFTER `cancellation_notes`;
ALTER TABLE `#__hotelreservation_offers` ADD COLUMN `offer_initial_price` FLOAT(10,2) NULL  AFTER `state` ;
ALTER TABLE `#__hotelreservation_applicationsettings` ADD `enable_children_categories` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_discounts` ADD `discount_type` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_confirmations` ADD COLUMN  `cubilis_status` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN  `room_view` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__hotelreservation_rooms` DROP INDEX `NewIndex`;
ALTER TABLE `#__hotelreservation_airport_transfer_types` DROP INDEX `NewIndex`;
