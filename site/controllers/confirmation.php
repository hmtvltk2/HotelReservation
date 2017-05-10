<?php
/**
* @copyright	Copyright (C) 2008-2013 CMSJunkie. All rights reserved.
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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JHotelReservationControllerConfirmation extends JControllerLegacy
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	 
	function __construct()
	{
		parent::__construct();
	}
	function viewConfirmation(){
		JRequest::setVar("view","confirmation");
		
		//check if view reservation is allowed 
		$app = JFactory::getApplication();
		$componentName = getBookingExtName();
		$currentReservation = JRequest::getVar("reservationId",-1);
		$sessionReservation = $app->getUserStateFromRequest( "$componentName.reservation_id", 'reservation_id', false);
		if($sessionReservation != $currentReservation) {
			$this->setRedirect(JRoute::_('index.php?option=' . getBookingExtName() . '&task=hotels.searchHotels'),$msg);
		}
		
		parent::display();
	}

	function sendGuestList(){
		$model = $this->getModel("Confirmation");
		$model->sendGuestList();
	}
	
}