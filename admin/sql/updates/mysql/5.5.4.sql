ALTER TABLE `#__hotelreservation_confirmations_payments` ADD COLUMN `processor_id` tinyint(1) unsigned NOT NULL DEFAULT '1' after `processor_type`;
