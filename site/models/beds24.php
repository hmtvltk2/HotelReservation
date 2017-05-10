<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modelitem');

/**
 *
 * @author George
 *
 */
class JHotelReservationModelBeds24 extends JModelItem{
	
	
	public function getNewReservations($hotelId){
		if(empty($hotelId))
			return null;
		$reservationsTable = $this->getTable("Confirmations");
		return $reservationsTable->getBeds24Reservations($hotelId, CUBILIS_MAX_RESERVATIONS);
	}
	
	public function setReservationBeds24Status($reservations){
		$reservationsTable = $this->getTable("Confirmations");
		$reservationsTable->setReservationBeds24Status($reservations);
	}
}