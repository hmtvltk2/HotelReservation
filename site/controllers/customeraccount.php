<?php
/**
* @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
defined('_JEXEC') or die('Restricted access');

class JHotelReservationControllerCustomerAccount extends JControllerLegacy{
	function __construct()
	{
		JRequest::setVar('view','customeraccount');
		parent::__construct();
	}
	
	function editAccount(){
		JRequest::setVar('layout','editAccount');
		return $this->display();
	}
	
	function back(){
		JRequest::setVar('view','customeraccount');
		return $this->display();
	}
	
	function setStatus(){
		$reservationId = JRequest::getInt('reservationId');
		$status = JRequest::getInt('statusId');

		$table = $this->getTable("Confirmations");
		$table->setStatus($reservationId, $status);
		$this->setRedirect( 'index.php?option='.getBookingExtName().'&task=customeraccount.managereservations&view=customeraccount', $msg );
	}
	
	function managereservations(){
		JRequest::setVar('layout','managereservations');
		return $this->display();
	}
		
	function editreservation(){
		JRequest::setVar('layout','editreservation');
		return $this->display();
	}
	
	function saveReservation(){
		$reservationId = JRequest::getInt('reservationId');
		if($this->save())
		$this->setRedirect(JRoute::_('index.php?option='.getBookingExtName().'&task=customeraccount.editreservation&reservationId='.$reservationId));
	}
	
	function saveCloseReservation(){
		if($this->save())
		$this->setRedirect( JRoute::_('index.php?option='.getBookingExtName().'&task=customeraccount.managereservations'));
	}
	function save()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN',true));
	
		$app      = JFactory::getApplication();
		$model = $this->getModel('customeraccount');
		$post = JRequest::get( 'post' );
		$data = JRequest::get( 'post' );
		$context  = 'com_jhotelreservation.edit.reservation';
		$task     = $this->getTask();
		$recordId = JRequest::getInt('reservationId');

		if (!$model->save($post)){
			// Save the data in the session.
			$app->setUserState('com_jhotelreservation.edit.reservation.data', $data);
			// Redirect back to the edit screen.
			$msg = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED').":".$model->getError();
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=customeraccount.editreservation&reservationId=' .$recordId),$msg);
			return false;
		}

		$this->setMessage(JText::_('LNG_RESERVATION_SAVE_SUCCESS',true));
		
		$reservationService = new ReservationService();
		$reservationDetails	=  $reservationService->getReservation($recordId);
		$emailService = new EmailService();
		$emailService->sendConfirmationEmail($reservationDetails,true);
		
		return true;
	}
	function cancelReservation(){
		
		$confirmationId = JRequest::getVar('reservationId');
		BookingService::cancelReservation($confirmationId);
		$msg = JText::_('LNG_RESERVATION_CANCELED');
		
		$reservationService = new ReservationService();
		$reservationDetails	=  $reservationService->getReservation($review->confirmation_id);
		$emailService = new EmailService();
		$emailService->sendCancelationEmail($reservationDetails);
		$this->setRedirect( JRoute::_('index.php?option='.getBookingExtName().'&task=customeraccount.managereservations'),$msg);
	}
	

}