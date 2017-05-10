DROP TABLE IF EXISTS `#__hotelreservation_hotel_review_comments`;
-- --------------------------------------------------------

--
-- Table structure for table `#__jhotelreservation_hotel_review_responses`
--

CREATE TABLE IF NOT EXISTS `#__hotelreservation_hotel_review_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reviewId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `comment` text,
  PRIMARY KEY (`id`,`reviewId`),
  KEY `R_19` (`reviewId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `#__hotelreservation_hotel_contacts` ADD COLUMN `booking_email` VARCHAR(45) NULL DEFAULT NULL AFTER `remail`;
ALTER TABLE `#__hotelreservation_hotel_contacts` ADD COLUMN `booking_list_email` VARCHAR(45) NULL DEFAULT NULL AFTER `booking_email`;
ALTER TABLE `#__hotelreservation_discounts` ADD COLUMN `reservation_cost_discount` TINYINT(1) NOT NULL DEFAULT '0' AFTER `discount_type`;
ALTER TABLE `#__hotelreservation_confirmations_extra_options` ADD COLUMN `extra_option_dates` VARCHAR(255) NULL AFTER `extra_option_days`;
ALTER TABLE `#__hotelreservation_extra_options` ADD COLUMN `commission`  TINYINT(4) NULL DEFAULT NULL AFTER `map_per_length_of_stay`;


ALTER TABLE `#__hotelreservation_applicationsettings` ADD COLUMN `send_cancellation_email_admin_only` TINYINT(1) NOT NULL DEFAULT '0' AFTER `enable_breadcrumb`;


CREATE TABLE IF NOT EXISTS `#__hotelreservation_points_of_interest` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `hotel_id` INT(11) NULL,
  `name` VARCHAR(45) NULL,
  `description` VARCHAR(255) NULL,
  `image` VARCHAR(45) NULL,
  `meta_title` VARCHAR(45) NULL,
  `meta_description` VARCHAR(255) NULL,
  `meta_keywords` VARCHAR(245) NULL,
  `ordering` INT(11) NOT NULL DEFAULT '0',
  `publish` TINYINT(1) NOT NULL DEFAULT '0',
  `poi_latitude` VARCHAR(45),
  `poi_longitude` VARCHAR(45),
  `poi_country_id` INT(10) NULL,
  `poi_county` VARCHAR(255) NULL,
  `poi_city` VARCHAR(255) NULL,
  `poi_address` VARCHAR(255) NULL,
  `poi_zipcode` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__hotelreservation_points_of_interest_pictures` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `poid` INT NOT NULL,
  `poi_picture_path` VARCHAR(245) NULL,
  `poi_picture_enable` TINYINT(1) NULL,
  PRIMARY KEY (`id`))ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__hotelreservation_changelog`
(`id` INT NOT NULL AUTO_INCREMENT,
  `reservation_id` INT(11) NULL,
  `date` TIMESTAMP NULL,
  `user_id` INT(11) NULL,
  `description` LONGTEXT NULL,
  PRIMARY KEY (`id`))ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__hotelreservation_permissions` (`name`, `code`, `description`) VALUES
  ('Income Report', 'income_report','Income Report'),
  ('Country Report', 'country_report','Country Report'),
  ('Offers Report', 'offers_report','Offers Report'),
  ('Commission Income', 'commission_report','Commission Income');




ALTER TABLE `#__hotelreservation_offers_rates`
ADD COLUMN `extra_night_price_1` DECIMAL(12,2) NOT NULL AFTER `base_children`,
ADD COLUMN `extra_night_price_2` DECIMAL(12,2) NOT NULL AFTER `extra_night_price_1`,
ADD COLUMN `extra_night_price_3` DECIMAL(12,2) NOT NULL AFTER `extra_night_price_2`,
ADD COLUMN `extra_night_price_4` DECIMAL(12,2) NOT NULL AFTER `extra_night_price_3`,
ADD COLUMN `extra_night_price_5` DECIMAL(12,2) NOT NULL AFTER `extra_night_price_4`,
ADD COLUMN `extra_night_price_6` DECIMAL(12,2) NOT NULL AFTER `extra_night_price_5`,
ADD COLUMN `extra_night_price_7` DECIMAL(12,2) NOT NULL AFTER `extra_night_price_6`;


UPDATE `#__hotelreservation_offers_rates` SET extra_night_price_1=extra_night_price,extra_night_price_2=extra_night_price,
extra_night_price_3=extra_night_price,extra_night_price_4=extra_night_price,extra_night_price_5=extra_night_price,
extra_night_price_6=extra_night_price,extra_night_price_7=extra_night_price;

CREATE TABLE IF NOT EXISTS `#__hotelreservation_confirmations_discounts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `reservation_id` INT(11) NULL,
  `discount_id` INT(11) NULL,
  `discount_code` VARCHAR(50) NULL,
  `name` CHAR(255) NULL,
  `value` FLOAT(18,2) NULL,
  `is_percent` TINYINT(1) NULL,
  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `#__hotelreservation_applicationsettings`
  ADD COLUMN `enable_google_tag_manager` TINYINT(1) NOT NULL DEFAULT '1',
  ADD COLUMN `google_tag_manager_id` VARCHAR(255) NULL DEFAULT NULL ,
  ADD COLUMN `enable_seo` TINYINT(1) NOT NULL DEFAULT '1',
  ADD COLUMN `rooms_left` INT(11) NULL;

ALTER TABLE `#__hotelreservation_offers` ADD COLUMN `last_minute` TINYINT(1) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS  `#__hotelreservation_rating_classification` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `hotel_id` INT(11) NULL,
  `min_rate` FLOAT(18,2) NULL,
  `max_rate` FLOAT(18,2) NULL,
  `ordering` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
  
  ALTER TABLE `#__hotelreservation_discounts` DROP INDEX `room_id_2`;

