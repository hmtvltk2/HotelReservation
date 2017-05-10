ALTER TABLE `#__hotelreservation_excursions` DROP INDEX `NewIndex`;

ALTER TABLE `#__hotelreservation_excursions`  ADD COLUMN `latitude` VARCHAR(45),
  ADD COLUMN `longitude` VARCHAR(45),
  ADD COLUMN `country_id` INT(10) NULL,
  ADD COLUMN `county` VARCHAR(255) NULL,
  ADD COLUMN `city` VARCHAR(255) NULL,
  ADD COLUMN `address` VARCHAR(255) NULL,
  ADD COLUMN `zipcode` VARCHAR(45) NULL;


CREATE TABLE IF NOT EXISTS `#__hotelreservation_viewed_properties` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `hotel_id` INT NULL,
  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
