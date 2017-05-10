ALTER TABLE `#__hotelreservation_offers` ADD COLUMN `airport_transfer_type_id` INT(10) NOT NULL DEFAULT 0 AFTER `top`;
ALTER TABLE `#__hotelreservation_extra_options` DROP INDEX `NewIndex`;


CREATE TABLE IF NOT EXISTS `#__hotelreservation_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `description` text,
  `publish_date` DATETIME DEFAULT NULL,
  `retrieve_date` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `enable_map` TINYINT(1) NOT NULL DEFAULT '1' AFTER `room_view`;
ALTER TABLE `#__hotelreservation_confirmations_rooms_airport_transfer` DROP INDEX `NewIndex`;

ALTER TABLE `#__hotelreservation_hotels` CHANGE COLUMN `hotel_short_description` `hotel_short_description` VARCHAR(255) NOT NULL;

ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `enable_breadcrumb` TINYINT(1) NOT NULL DEFAULT '1' AFTER `enable_map`;

--
-- Table structure and creation for guest details information attributes settings
--
CREATE TABLE IF NOT EXISTS `#__hotelreservation_guest_details_attributes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(55) NULL ,
  `config_type` TINYINT(1) NULL ,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unique_Name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__hotelreservation_guest_details_attributes` (`id`, `name`, `config_type`) VALUES
  (2,  'salutation', 2),
  (3,  'first_name', 2),
  (4,  'last_name', 2),
  (5,  'company_name', 1),
  (6, 'address', 2),
  (7, 'postal_code', 2),
  (8, 'city', 2),
  (9, 'state_name', 2),
  (10, 'country', 2),
  (11, 'phone', 2),
  (12,  'email', 2),
  (13, 'remarks', 1);

CREATE TABLE `#__hotelreservation_offers_extra_options` (
  `extra_option_id` INT(11) NULL DEFAULT NULL,
  `offer_id` INT(11) NULL DEFAULT NULL)ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `#__hotelreservation_confirmations_rooms_airport_transfer`
ADD COLUMN `included_offer` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `#__hotelreservation_children_categories` ADD COLUMN `name` VARCHAR(45) NULL AFTER `hotel_id`;