<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/models/reservation.php' );

class JHotelReservationModelCustomerAccount extends JHotelReservationModelReservation
{
	public function populateState(){

		parent::populateState();
	}
	

	function getClientData(){
		// Get client data
		$table = $this->getTable('confirmations');
		$table = $table->getClientReservations(JFactory::getUser()->id);

		return $table;
	}
	
	function saveReservationInfo(){
		$table = $this->getTable('managehotelusers');
		$post = JRequest::get('post');
		$userData = $table->getUserById(JFactory::getUser()->id);
		$table->load($userData->id);

		if(!$table->bind($post)){
			return JText::_('LNG_RESERVATION_INFO_ERROR',true);
		}
		
		if(!$table->store()){
			return JText::_('LNG_RESERVATION_INFO_ERROR',true);
		}
		return JText::_('LNG_RESERVATION_INFO_SAVED',true);
	}
	function getClientReservations(){
		$table = $this->getTable('confirmations');
		$confirmations = $table->getClientReservations(JFactory::getUser()->id);
		return $confirmations;
	}
	function getCancellationDetails($hotelId){
		$table = $this->getTable('paymentsettings');
		return $table->getCancellationDetails($hotelId);
	}
	function getReservation(){
		$confirmationId = JRequest::getVar('confirmation_id');
		$table = $this->getTable('confirmations');
		$table->load($confirmationId);
		return $table;
	}


}

