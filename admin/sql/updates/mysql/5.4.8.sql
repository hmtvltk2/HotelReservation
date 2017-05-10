CREATE TABLE IF NOT EXISTS `#__hotelreservation_children_categories_prices` (
  `rate_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`rate_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hotelreservation_children_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` tinyint(10) NOT NULL,
  `min_age` tinyint(3) NOT NULL,
  `max_age` tinyint(3) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__hotelreservation_children_categories_rate_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `category_id` tinyint(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;